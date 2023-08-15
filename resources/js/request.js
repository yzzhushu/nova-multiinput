export default {
    props: ['field'],

    data() {
        return {
            loading: false
        }
    },

    methods: {
        loadLists() {
            if (typeof this.field.options !== 'string') return;
            if (this.loading) return;
            this.loading = true;

            const value = this.field.value;
            if (value.length === 0) return;

            Nova
                .request()
                .post(this.field.options, {q: value.join(',')})
                .then(response => {
                    console.log(response.data.data)
                    this.lists = response.data.data;
                });
        }
    }
}
