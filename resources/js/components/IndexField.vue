<template>
    <div :class="`text-${field.textAlign}`">
        <div v-if="field.value.length > 0">
            <Tooltip
                :triggers="['hover']"
                :popperTriggers="['hover']"
                placement="top"
                theme="plain"
                @show="loadLists"
                :auto-hide="true"
                distance="12"
            >
                <template #default>
                    <span class="link-default">{{ display }}</span>
                </template>
                <template #content>
                    <HxTable
                        :columns="columns"
                        :lists="lists"
                        class="min-w-[24rem] max-w-2xl"
                        style="max-height: calc(360px + .5rem)"
                    />
                </template>
            </Tooltip>
        </div>
        <p v-else>&mdash;</p>
    </div>
</template>

<script>
import request from "../request";
export default {
    mixins: [request],

    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            lists: []
        }
    },

    computed: {
        columns() {
            return this.field.columns;
        },

        display() {
            return this.field.displayedAs || this.field.value.length;
        }
    }
}
</script>
