<template>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form @submit.prevent="store">
                <textarea placeholder="What's going on ?" class="rounded-lg border border-gray-200 w-full p-2 font-semibold resize-none focus:outline-none" v-model="content"></textarea>
                <span class="my-5 text-red-500" v-if="$page.props.errors">
                    {{ $page.props.errors.content }}
                </span>
                <div class="flex items-center space-x-4 justify-end mt-3">
                    <p class="text-sm text-gray-400 font-thin" :class="{ 'text-red-500' : remainingChars < 0 }">{{remainingChars}} caractères restants</p>
                    <button-vue :disabled="!canSubmit" class="bg-blue-500 hover:bg-blue-800 rounded-full font-extrabold">Tweet</button-vue>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    
    import Button from '../../Jetstream/Button.vue'
    export default {
        components: { 
            ButtonVue: Button 
        },
        data() {
            return {
                content: '',
                limit: 280
            }
        },
        methods: {
            store() {
                this.$inertia.post('tweets',
                    { content : this.content },
                    { preserveState: false }
                )
            },

       },
       computed:{
           remainingChars() {
               return this.limit - this.content.length
           },
           canSubmit() {
               return this.content.length && this.remainingChars >= 0
           }
       }
    }
</script>

<style scoped>
    button:disabled {
        opacity: 50%;
        cursor: not-allowed;
    }
</style>