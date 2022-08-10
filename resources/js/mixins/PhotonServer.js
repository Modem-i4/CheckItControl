export default {
    data() {
        const Status = {
            ordered: 'ordered',
            launched: 'launched',
            started: 'started',
            finished: 'finished',
        };
        return {
            PhotonServer: null,
            maxPlayers: 16,
            room: null,
            group: [],
            playersNow: 0,
            canForceStart: false,
            canRestartGame: false,
            statusString: "Завантаження...",
            Status,
            status: null,
            statusChecker: null,
            remainedTime: null,
            timeStarted: null,
            gameStarted: 0,
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
            console.log("UPD MASTER")
            console.log(this.PhotonServer.roomInfosDict)
            if(this.room === undefined) {
                if(this.statusString === "Очікує на гравців") {
                    this.statusString = "З'єднано\nЧас очікування а гравців сплинув\nВи можете перезапустити гру"
                    this.canRestartGame = true
                }
                else if(this.statusString === "Гра триває!") {
                    this.setLessonStatus(this.Status.finished)
                }
                return;
            }
            try {
                //reset empty room
                if(this.playersNow > 1 && this.room.playerCount === 0) {
                    this.group.forEach(o => { if(o.status === 1) o.status = 0 })
                    this.setStudentsStats()
                }
                this.playersNow = this.room.playerCount
            }
            catch (e) {}
            if(this.status === this.Status.launched) {
                this.statusString = "Очікує на гравців"
                console.log("Очікує на гравців/this.status")
                this.canForceStart = this.playersNow > 1;
            }
        },
        initPhoton() {
            if (this.PhotonServer === null) {
                this.statusString = "З'єднання..."
                this.PhotonServer = new Photon.LoadBalancing.LoadBalancingClient(this.AppInfo.Wss, this.AppInfo.AppId, this.AppInfo.AppVersion);
                this.PhotonServer.onEvent = this.onEvent
                this.PhotonServer.onJoinRoom = this.onJoinRoom
                this.PhotonServer.onError = this.onError
                this.PhotonServer.onRoomListUpdate = this.UpdMaster
                this.PhotonServer.onStateChange = this.onStateChange
            }
            if(!this.PhotonServer.isConnectedToMaster())
                this.PhotonServer.connectToNameServer({region: this.AppInfo.AppLocation}); //, lobbyName: this.AppInfo.LobbyName, lobbyType: Photon.LoadBalancing.Constants.LobbyType.Default
        },
        createRoom() {
            this.statusString = "Створення кімнати..."
            let roomCreator = window.setInterval(() => {
                if(this.PhotonServer.isConnectedToMaster() && (this.lesson.students || this.lesson.group_id === null)) {
                    console.log("TRY!!!!!")
                    this.PhotonServer.createRoom(this.lesson.code)
                    this.UpdMaster()
                    clearInterval(roomCreator)
                }
            }, 500)
        },
        async onStateChange(e) {
            console.log(`onStateChange: ${e}`)
            if(e === 5) { // ConnectedToMaster -> JoinedLobby
                if(this.statusString !== "Створення кімнати..." && this.statusString !== "Гра триває!")
                    this.statusString = "З'єднано"
                await new Promise(resolve => setTimeout(resolve, 1000))
                this.canRestartGame = false
                switch (this.status) {
                    case this.Status.ordered :
                        this.createRoom()
                        break;
                    case this.Status.launched :
                        console.log(this.PhotonServer.roomInfosDict)
                        if (this.PhotonServer.roomInfosDict[this.lesson.code] === undefined) {
                            this.statusString = "З'єднано\nЧас очікування на гравців сплинув\nВи можете перезапустити гру"
                            this.canRestartGame = true
                        }
                        break;
                }
                this.UpdMaster()
            }
        },
        restartGame() {
            this.statusString = "Перезапуск..."
            this.canRestartGame = false
            this.createRoom()
        },
        onJoinRoom() {
            try {
                if(this.playersNow < 2) {
                    this.setRoomDefaults()
                    this.setLessonStatus(this.Status.launched)
                }
                else {
                    this.PhotonServer.raiseEvent(101, "1") // start game
                    this.onGameStarted()
                    this.room.setIsOpen(false)
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
            if(this.lesson.group_id) {
                let photonGroupMinimized = []
                this.group.forEach(o => {photonGroupMinimized.push([[o.name],[o.status]])})
                console.log(`ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZzz`)
                console.log(JSON.stringify(photonGroupMinimized))
                this.PhotonServer.currentRoom.setCustomProperty("group", `{"c2array":true,"size":[${this.group.length},2,1],"data":${JSON.stringify(photonGroupMinimized)}}`);
            }
        },
        onEvent(code, content, actorNr) {
            switch (code) {
                case 101:
                default:
            }
        },
        onError(code, msg) {
            console.log(`code: ${code}`)
            switch (code) {
                case 1003:
                    console.log(`case`)
                    if(this.status !== this.Status.finished) {
                        console.log(`initPhoton:`)
                        this.initPhoton()
                    }
                    break;
                default:
                    this.statusString = "Помилка:\n" + msg
            }
        },
        forceStart() {
            this.PhotonServer.joinRoom(this.lesson.code)
        },
        onGameStarted() {
            this.canForceStart = false
            console.log("Гру розпочато!/onGameStarted")
            let gameStartWaiter = window.setInterval(() => {
                console.log(this.room.getCustomProperty("timeStarted"))
                if(this.room.getCustomProperty("timeStarted") !== undefined) {
                    this.statusString = "Гру розпочато!"
                    this.applyStatus(this.Status.started)
                    this.setLessonStatus(this.Status.started)
                    clearInterval(gameStartWaiter)
                }
            }, 500)
        },
    },
};
