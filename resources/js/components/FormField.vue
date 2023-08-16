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
import request from "../request";

export default {
    mixins: [FormField, HandlesValidationErrors, request],

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
            if (exists + latest > _limit) {
                Nova.error('累计最多录入' + _limit + '条记录');
                Nova.error('已存在：' + exists + '，本次新录入：' + latest);
                return;
            }
            const options = this.field.options;
            if (typeof options === 'string') {
                this.loadLists();
            } else {
                Nova.error('暂不支持该类型');
            }
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
            let intInt = this.field.formatInt;
            input.split(',').map((item) => {
                if (item === '') return;
                item = intInt ? parseInt(item) : item.toString();
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
            const value = this.field.value;
            if (value !== null) {
                this.handleInput(value.join(','));
            }
        },

        // 保存数据
        fill(formData) {
            formData.append(this.fieldAttribute, JSON.stringify(Object.keys(this.check)))
        },
    },
}
</script>
