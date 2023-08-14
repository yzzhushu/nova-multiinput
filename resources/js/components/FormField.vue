<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <Splitter
                style="height: calc(400px + .5rem)"
                :class="errorClasses"
            >
                <SplitterPanel>
                    <HxTable
                        :columns="columns"
                        :lists="lists"
                        style="min-height: 400px"
                        @row:selected="removeInput"
                    />
                </SplitterPanel>
                <SplitterPanel>
                    <HxInput
                        :isTextArea="true"
                        customClass="py-3"
                        customStyle="height: calc(100% - 1.5rem)"
                        :placeholder="field.placeholder || '请输入'"
                        :value="input"
                        @update:value="handleInput"
                    />
                </SplitterPanel>
            </Splitter>
        </template>
    </DefaultField>
</template>

<script>
import {FormField, HandlesValidationErrors} from 'laravel-nova';

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            lists: [],
            check: {},
            input: '',
            index: 'id',
            columns: [],
        }
    },

    methods: {

        removeInput(column) {
            const index = column.data[this.index];
            delete this.check[index];
            this.drawTable();
        },

        handleInput(value) {
            let latest = this.formatInput(value);
            let _limit = this.field.limit || 500;
            if (latest > _limit) {
                Nova.error('单次最多录入' + _limit + '条记录');
                Nova.error('请删减后重新录入');
                return;
            }
            const exists = this.lists.length;
            if (exists + latest >= _limit) {
                Nova.error('累计最多录入' + _limit + '条记录');
                Nova.error('已录入：' + exists + '，本次新录入：' + latest);
                return;
            }
            const options = this.field.options;
            if (typeof options === 'string') {
                this.requestLists();
            } else {
                if (typeof options !== 'object' || options.length === 0) {
                    Nova.error('请先配置options选项');
                    return;
                }
            }
        },

        // 绘制列表数据
        drawTable() {
            let lists = [];
            let index;
            for (index in this.check) {
                lists.push(this.check[index]);
            }
            this.lists = lists;
        },

        // 请求获取数据
        requestLists() {
            Nova
                .request()
                .post(this.field.options, {q: this.input})
                .then(response=> {
                    const result = response.data.data;
                    if (result.length === 0) return;

                    result.map(item => {
                        if (item[this.index] === undefined) return;
                        this.check[item[this.index]] = item;
                    });
                    this.drawTable();
                });
        },

        // 处理输入内容
        formatInput(value) {
            let input = value
                .replaceAll(' ', '')
                .replaceAll('\n', ',')
                .replaceAll(/\W+/g, ',');
            if (!this.field.supportABC)
                input = input.replaceAll(/\D+/g, ',') + ',';

            let filter = {};
            let unique = 0;
            input.split(',').map((item) => {
                if (item === '') return;
                filter[item] = true;
                unique++;
            });
            this.input = Object.keys(filter).join(',');
            return unique;
        },

        setInitialValue() {
            this.columns = this.field.columns;
            if (typeof this.columns[0] === 'object' &&
                this.columns[0].field !== undefined)
                this.index = this.columns[0].field;

            // this.value = this.field.value || ''
        },

        fill(formData) {
            formData.append(this.fieldAttribute, JSON.stringify(Object.keys(this.check)))
        },
    },
}
</script>
