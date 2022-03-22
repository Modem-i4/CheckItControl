export default {
    methods: {
        fav(item) {
            item.loading = true
            axios.patch(`/api/fav/${item.id}`, {
                favorite: !item.favorite
            })
                .then(r => {
                    item.favorite = !item.favorite
                    item.loading = false
                })
        },
    }
}
