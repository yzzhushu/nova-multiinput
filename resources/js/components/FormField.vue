<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <Splitter
                style="height: 320px"
                :class="errorClasses"
            >
                <SplitterPanel>
                    <HxTable :columns="columns" :lists="lists" @row:selected="aaaaaa"/>
                </SplitterPanel>
                <SplitterPanel>
                    <div>fawfaw</div>
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
            lists: [
                {id: 1, name: 'n1'},
                {id: 2, name: 'n2'},
                {id: 3, name: 'n3'},
                {id: 4, name: 'n4'},
                {id: 5, name: 'n5'},
                {id: 6, name: 'n6'},
                {id: 7, name: 'n7'},
                {id: 8, name: 'n8'},
                {id: 9, name: 'n9'},
                {id: 10, name: 'n10'},
                {id: 11, name: 'n11'},
                {id: 12, name: 'n12'},
                {id: 13, name: 'n13'},
                {id: 14, name: 'n14'},
            ],
            columns: [
                {field: 'id', header: '编码'},
                {field: 'name', header: '名称'},
            ]
        }
    },

    methods: {
        aaaaaa(e) {
            console.log(11111);
            console.log(e.data);
        },

        setInitialValue() {
            this.value = this.field.value || ''
        },

        fill(formData) {
            formData.append(this.fieldAttribute, this.value || '')
        },
    },
}
</script>
