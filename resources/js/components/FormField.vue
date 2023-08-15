<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <Splitter
                style="height: calc(360px + .5rem)"
                :class="errorClasses"
            >
                <SplitterPanel>
                    <HxTable
                        :columns="columns"
                        :lists="lists"
                        style="min-height: 360px"
                        @row:selected="removeInput"
                    />
                </SplitterPanel>
                <SplitterPanel>
                    <div class="w-full h-full p-3 px-0">
                        <textarea
                            class="w-full h-full form-control form-input form-input-bordered
                                border-none outline-none px-0 resize-none text-base"
                            style="box-shadow: none;"
                            :placeholder="field.placeholder || '请输入'"
                            v-model="value"
                            @change="handleInput($event.target.value)"
                        ></textarea>
                    </div>
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
            value: '',
            index: 'id',
            columns: [],
        }
    },

    methods: {

        removeInput(column) {
            const index = column.data[this.index];
            delete this.check[index];
            this.lists.splice(column.index, 1);
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
                Nova.error('已存在：' + exists + '，本次新录入：' + latest);
                return;
            }
            const options = this.field.options;
            if (typeof options === 'string') {
                this.requestLists();
            } else {
                Nova.error('咱不支持该类型');
            }
        },

        // 绘制列表数据
        drawTable() {
            let lists = [];
            const int = this.field.formatInt;
            Object.keys(this.check).sort((a, b) => {
                if (int) return parseInt(a) - parseInt(b);
                return a.localeCompare(b);
            }).map((item) => {
                lists.push(this.check[item]);
            });
            this.lists = lists;
        },

        // 请求获取数据
        requestLists() {
            const q = this.value;
            if (q === '') return;
            this.value = '数据解析中...';

            Nova
                .request()
                .post(this.field.options, {q: q})
                .then(response=> {
                    this.value = '';
                    const result = response.data.data;
                    if (result.length === 0)
                        return Nova.error('未找到匹配的数据');
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
                input = input.replaceAll(/\D+/g, ',');

            let filter = [];
            let errors = {};
            input.split(',').map((item) => {
                if (item === '') return;
                if (this.field.formatInt)
                    item = parseInt(item);
                if (this.check[item] !== undefined)
                    return errors[item] = true;
                if (filter.includes(item))
                    return errors[item] = true;
                filter.push(item);
            });
            errors = Object.keys(errors);
            if (errors.length > 0) {
                Nova.error('以下内容重复，已自动过滤：' + errors.join(','));
            }
            this.value = filter.join(',');
            return filter.length;
        },

        // 初始化数据
        setInitialValue() {
            this.columns = this.field.columns;
            if (typeof this.columns[0] === 'object' &&
                this.columns[0].field !== undefined)
                this.index = this.columns[0].field;

            const initValue = this.field.value;
            if (initValue !== null) {
                this.handleInput(initValue.join(','));
            }
        },

        fill(formData) {
            formData.append(this.fieldAttribute, JSON.stringify(Object.keys(this.check)))
        },
    },
}
</script>
