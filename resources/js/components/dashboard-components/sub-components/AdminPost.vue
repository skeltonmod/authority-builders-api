<template>
    <div>
        <p class="text-h5 --primary">Create post</p>
        <v-container class="d-flex">
            
            <v-row class="justify-center">
                <v-col cols="12" md="6">
                    <v-sheet
                        color="white"
                        elevation="10"
                        style="height: auto; padding: 1em"
                        width="769"
                    >
                    <div v-html="compiledMarkdown"></div>
                    </v-sheet>
                </v-col>
            </v-row>
        </v-container>
        <v-container class="d-flex">
            <v-row class="justify-center">
                <v-col cols="12" md="6">
                    <v-textarea
                        outlined
                        name="input-7-4"
                        label="Outlined textarea"
                        v-model="input"
                    ></v-textarea>
                </v-col>
            </v-row>
        </v-container>

        

        
    </div>
</template>

<script>
import marked from "marked";
export default {
    data: () => ({
        input: "# Add your text Here",
        timeout: null
    }),

    mounted() {
        console.log(marked);
    },

    computed: {
        compiledMarkdown: function() {
            return marked(this.input, { sanitize: true });
        }
    },

    methods: {
        update: {
            get() {
                return this.input;
            },
            set(val) {
                if (this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.input = val;
                }, 300);
            }
        }
    }
};
</script>
