
export default {
    data() {
        return {
            pagination: {},
            search: '',
            searches:{},
            items: [],
            newItem: {},
            repositoryFilters: {},

            loading: true,
            options: {
                itemsPerPage: 10,
                sortDesc : 'desc',
            },
            footerOptions: {
                itemsPerPageOptions: [10, 25, 50, 100],
            },

            CRUD: '',
            headers: [],
            defaultSortByField: 'id',
            defaultSortDirection: 'desc',
        }
    },
    computed: {
        sortBy() {
            if (this.options.sortBy === undefined || this.options.sortBy.length === 0) {    // If no column is selected for sorting, return the default value
                return this.defaultSortByField;
            }
            return this.options.sortBy[0];
        },
        sortDirection() {
            if(this.options.sortDesc[0] === 'd') {
                return this.defaultSortDirection;
            }
            return this.sortBy == null ? null : this.options.sortDesc[0] ? 'asc' : 'desc';
        },
    },
    watch: {
        options: {
            handler() {
                this.fetch()
            },
            deep: true,
        },
        search: _.debounce(function () {
            this.applySearch()

        }, 400),
        searches: {
            handler() {
                this.applySearch()
            },
            deep: true,
        }
    },
    mounted() {
        this.applySearch();
    },
    methods: {
        applySearch() {
            this.options.page = 1;
            this.fetch()
        },
        fetch() {
            this.loading = true;
            axios.get(this.CRUD, {
                params: {
                    search: (this.search === '' ? null : this.search),
                    searches: (this.searches === [] ? null : this.searches),
                    repositoryFilters: (this.repositoryFilters === {} ? null : this.repositoryFilters),

                    page: this.options.page,
                    perPage: this.options.itemsPerPage,

                    sortBy: this.sortBy,
                    sortDirection: this.sortDirection,
                }
            }).then(response => {
                this.items = response.data.data;
                this.pagination.total = response.data.total;
                this.loading = false;
            })
        },
    }
}
