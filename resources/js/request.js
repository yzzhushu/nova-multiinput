export default {
    props: ['index', 'field'],

    data() {
        return {
            loading: false,
            check: {},
            lists: [],
            value: '',
            index: 'id'
        }
    },

    mounted() {
        if (typeof this.columns[0] === 'object' &&
            this.columns[0].field !== undefined)
            this.index = this.columns[0].field;
        console.log('MultiInput index key: ' + this.index);
    },

    methods: {
        // 获取数据
        loadLists() {
            if (this.loading) return;
            if (this.value === '') return;
            this.loading = true;

            Nova
                .request()
                .post(this.field.options, {q: this.value})
                .then(response=> {
                    this.loading = false;

                    const result = response.data.resources;
                    if (result.length === 0)
                        return Nova.error('未匹配到数据');
                    result.map(item => {
                        if (item[this.index] === undefined) return;
                        this.check[item[this.index]] = item;
                    });

                    this.drawTable();
                });
        },

        // 绘制列表数据
        drawTable() {
            let lists = [];
            const parse = this.field.formatInt;
            Object.keys(this.check).sort((a, b) => {
                if (parse) return parseInt(a) - parseInt(b);
                return a.localeCompare(b);
            }).map((item) => {
                lists.push(this.check[item]);
            });
            this.lists = lists;
            this.value = '';
        }
    },

    computed: {
        columns() {
            return this.field.columns;
        }
    }
}
