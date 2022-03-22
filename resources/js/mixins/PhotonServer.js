export default {
    data() {
        return {
            PhotonServer: null,
            maxPlayers: 16,
            room: null,
            group: [],
            playersNow: 0,
            canForceStart: false,
            canRestartGame: false,
            forceStarted: false,
            status: "Завантаження...",
            remainedTime: null,
            timeStarted: null,
            gameStarted: 0,
            roundDuration: 200,
            AppInfo : {
                Wss: 1,
                AppId: "80143746-9e60-4563-a78a-e62fbd55a711",
                AppVersion: "1.0",
                AppLocation: "RU",
                LobbyName: "Default"
            }
        }
    },
    methods: {
        UpdMaster() {
            this.room = this.PhotonServer.roomInfosDict[this.lesson.code]
            console.log(this.room)
            if(this.room === undefined || this.room.getCustomProperty("finished")) {
                this.testStatsUploaded()
                return;
            }
            try {
                this.playersNow = this.room.playerCount
                if(this.lesson.group_id)
                    this.group = JSON.parse(this.room.getCustomProperty("group")).data
            }
            catch (e) {}
            if(this.gameStarted === 0) {
                if(!this.room.isOpen || this.forceStarted) {
                    this.canForceStart = false
                    this.status = "Гра триває!"
                    this.timeStarted = Date.now()
                    this.gameStarted = 1
                    let roundTimer = window.setInterval(() => {
                        let remainedTime = this.roundDuration-Math.round((Date.now() - this.timeStarted)/1000)
                        if(remainedTime < 0) {
                            this.gameStarted = 2
                            this.remainedTime = null
                            clearInterval(roundTimer)
                        }
                        else {
                            this.remainedTime = `${Math.floor(remainedTime/60)}:${String(remainedTime%60).padStart(2, '0')}`
                        }
                    }, 1000)
                }
                else {
                    this.status = "Очікує на гравців"
                    this.canForceStart = this.playersNow > 1;
                }
            }
        },
        initPhoton() {
            this.testStatsUploaded()
            if(this.status !== "Гру завершено") {
                this.PhotonServer = new Photon.LoadBalancing.LoadBalancingClient(this.AppInfo.Wss, this.AppInfo.AppId, this.AppInfo.AppVersion);
                this.PhotonServer.onEvent = this.onEvent
                this.PhotonServer.onJoinRoom = this.onJoinRoom
                this.PhotonServer.onError = this.onError
                this.PhotonServer.onRoomListUpdate = this.UpdMaster
                this.PhotonServer.onPropertiesChange = this.PropertyChange
                this.PhotonServer.connectToNameServer({ region: this.AppInfo.AppLocation}); //, lobbyName: this.AppInfo.LobbyName, lobbyType: Photon.LoadBalancing.Constants.LobbyType.Default
                this.status = "З'єднано"
                this.UpdMaster()
            }
        },
        createRoom() {
            this.PhotonServer.createRoom(this.lesson.code)
            console.log(this.PhotonServer.currentRoom)
        },
        onJoinRoom() {
            try {
                if(this.playersNow < 2)
                    this.setRoomDefaults()
                else {
                    this.PhotonServer.raiseEvent(101, "1") // start game
                    this.room.setIsOpen(false)
                    //this.onGameStarted()
                    this.PhotonServer.currentRoom.setMaxPlayers(this.room.playerCount-1)
                }
            }
            catch (e) { }
            this.PhotonServer.leaveRoom()
        },
        setRoomDefaults() {
            this.PhotonServer.currentRoom.setMaxPlayers(this.maxPlayers);
            this.PhotonServer.currentRoom.setEmptyRoomLiveTime(300000)
            this.PhotonServer.currentRoom.setCustomProperty("lessonId",this.lesson.id);
            this.PhotonServer.currentRoom.setCustomProperty("topic",this.lesson.quiz_id);
            this.PhotonServer.currentRoom.setCustomProperty("subjectId",this.lesson.subject_id);
            if(this.lesson.group_id)
                this.PhotonServer.currentRoom.setCustomProperty("group", `{"c2array":true,"size":[${this.group.length},2,1],"data":${JSON.stringify(this.group)}}`);
        },
        onEvent(code, content, actorNr) {
            switch (code) {
                case 101:
                    this.onGameStarted()
                default:
            }
        },
        onError(code, msg) {
            console.log(code)
            switch (code) {
                default:
                    this.status = "Помилка:\n" + msg
            }
        },
        forceStart() {
            this.PhotonServer.joinRoom(this.lesson.code)
            this.forceStarted = true
        },
        onGameStarted() {
            this.canForceStart = false
            this.status = "Гру запущено!"
        },
        testStatsUploaded() {
            console.log("KURWA")
            axios.get(`${this.CRUD}/testStats/${this.id}`)
                .then(response => {
                    console.log("testStats: " + response.data)
                    if(response.data > 0) {
                        this.status = "Гру завершено"
                        this.getQuizStats()
                    }
                    else {
                        this.canRestartGame = true
                    }
                })
        },
        PhotonStarted() {
            let roomCreator = window.setInterval(() => {
                if(this.PhotonServer.isConnectedToMaster() && (this.lesson.students || this.lesson.group_id === null)) {
                    this.createRoom()
                    window.history.pushState('page2', 'Title', `${window.location.pathname}/?started`);
                    clearInterval(roomCreator)
                }
            }, 500)
        },
        restartGame() {
            window.history.pushState('page2', 'Title', `${window.location.pathname}/?new`);
            this.status = "Перезапуск..."
            this.canRestartGame = false
            this.PhotonStarted()
        },
        PropertyChange(propertyChanged) {
            console.log(propertyChanged)
            switch(propertyChanged) {
                //case :

            }
        },
    },
};
