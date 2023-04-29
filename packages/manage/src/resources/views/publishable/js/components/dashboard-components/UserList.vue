<template>
    <v-app id="inspire">
        <Nav />
        <v-card class="mx-auto pa-4 ma-12" width="80%">
            <v-data-table :headers="headers" :items="users" class="elevation-1">
                <template v-slot:top>
                    <v-toolbar flat>
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" max-width="500px">
                            <template v-slot:activator="{ on, attrs }">
                                <v-container>
                                    <v-row>
                                        <v-col class="d-flex justify-end">
                                            <v-btn
                                                color="primary"
                                                dark
                                                class="mb-2 mr-4"
                                                v-bind="attrs"
                                                v-on="on"
                                                @click="crud = true"
                                            >
                                                New Item
                                            </v-btn>
                                            <v-btn
                                                color="primary"
                                                dark
                                                class="mb-2"
                                                v-bind="attrs"
                                                v-on="on"
                                                @click="crud = false"
                                            >
                                                Invite User
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </template>

                            <v-card v-if="crud">
                                <v-card-title>
                                    <span class="text-h5">{{ formTitle }}</span>
                                </v-card-title>
                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field
                                                    v-model="editedItem.name"
                                                    label="Name"
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="7" md="4">
                                                <v-text-field
                                                    v-model="editedItem.id"
                                                    label="ID"
                                                    readonly
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-combobox
                                                    label="Roles"
                                                    :items="roles"
                                                    item-text="name"
                                                    item-value="id"
                                                    v-model="editedItem.role"
                                                ></v-combobox>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field
                                                    v-model="editedItem.email"
                                                    label="Email"
                                                    email
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" sm="6" md="4">
                                                <v-text-field
                                                    v-model="editedItem.status"
                                                    label="Status"
                                                    readonly
                                                ></v-text-field>
                                            </v-col>

                                            <v-col cols="12" sm="6" md="4">
                                                <v-combobox
                                                    label="Organizations"
                                                    :items="organizations"
                                                    item-text="name"
                                                    item-value="id"
                                                    v-model="
                                                        editedItem.organization
                                                    "
                                                ></v-combobox>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        color="blue darken-1"
                                        text
                                        @click="close"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        color="blue darken-1"
                                        text
                                        @click="save"
                                    >
                                        Save
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                            <v-card v-else>
                                <v-card-title>
                                    <span class="text-h5">Invite User</span>
                                </v-card-title>
                                <v-card-text>
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="6" md="12">
                                                <v-text-field
                                                    v-model="invite_email"
                                                    label="Email Address to Invite"
													:rules="emailRules"
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        color="blue darken-1"
                                        text
                                        @click="close"
                                    >
                                        Cancel
                                    </v-btn>
                                    <v-btn
                                        color="blue darken-1"
                                        text
                                        @click="invite"
                                    >
                                        Invite User
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                        <v-dialog v-model="dialogDelete" max-width="500px">
                            <v-card>
                                <v-card-title class="text-h5"
                                    >Are you sure you want to delete this
                                    item?</v-card-title
                                >
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        color="blue darken-1"
                                        text
                                        @click="closeDelete"
                                        >Cancel
                                    </v-btn>
                                    <v-btn
                                        color="blue darken-1"
                                        text
                                        @click="deleteItemConfirm"
                                        >OK</v-btn
                                    >
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon small class="mr-2" @click="editItem(item)">
                        mdi-pencil
                    </v-icon>
                    <v-icon small @click="deleteItem(item)">
                        mdi-delete
                    </v-icon>
                </template>
                <template v-slot:no-data>
                    <v-btn color="primary" @click="initialize">
                        Reset
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
    </v-app>
</template>

<script>
import apiRoutes from "../../apiRoutes";
export default {
    // Copy pasted from vuetify docs https://vuetifyjs.com/en/components/data-tables/#server-side-paginate-and-sort
    data: () => ({
        dialog: false,
        dialogDelete: false,
        // Controls if you want to add users manually or invite them through email
        crud: false,
        headers: [
            {
                text: "Name",
                align: "start",
                sortable: false,
                value: "name"
            },
            { text: "Role", value: "role" },
            { text: "Email", value: "email" },
            { text: "Status", value: "status" },
            { text: "Actions", value: "actions", sortable: false }
        ],
        users: [],
        roles: [],
        organizations: [],
        invite_email: "",
        current_role: "",
        current_organization: "",
        editedIndex: -1,
        editedItem: {
            id: 0,
            name: "",
            role: "",
            email: "",
            status: "",
            organization: ""
        },
        defaultItem: {
            name: "",
            role: "",
            email: "",
            status: ""
        },
		emailRules: [
			v => !!v || "Email is Required",
			v => /.+@.+/.test(v) || "Invalid Email"
		]
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? "New Item" : "Edit Item";
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        },
        dialogDelete(val) {
            val || this.closeDelete();
        }
    },

    created() {
        this.initialize();
    },

    methods: {
        initialize: async function() {
            const users = await apiRoutes.getusers();
            const roles = await apiRoutes.getroles();
            const organizations = await apiRoutes.getorganizations();
            // Use only one temporary array
            let temp_array = [];
            users.forEach(function(value, index) {
                temp_array.push({
                    id: value.id,
                    name: value.name,
                    email: value.email,
                    role: value.roles[0].name,
                    status: value.status,
                    organization: value.organization
                });
            });
            this.users = temp_array;

            // clear the temporary array?
            temp_array = [];
            roles.forEach(function(value, index) {
                temp_array.push({
                    id: value.id,
                    name: value.name
                });
            });

            this.roles = temp_array;

            // clear the temporary array?
            temp_array = [];
            organizations.forEach(function(value, index) {
                temp_array.push({
                    id: value.id,
                    name: value.name
                });
            });

            this.organizations = temp_array;
        },

        editItem(item) {
            this.crud = true;
            this.editedIndex = this.users.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
            this.current_role = this.editedItem.role;
            this.editItem.current_organization = this.editedItem.organization;
            // console.log(this.current_role);
        },

        deleteItem(item) {
            this.editedIndex = this.users.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialogDelete = true;
        },

        deleteItemConfirm() {
            this.users.splice(this.editedIndex, 1);
            this.closeDelete();
        },

        close() {
            this.dialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
            this.crud = true;
        },

        closeDelete() {
            this.dialogDelete = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
            this.crud = true;
        },

        save() {
            if (this.editedIndex > -1) {
                const request = {};
                for (let key in this.editedItem) {
                    if (key === "role") {
                        request[key] = this.editedItem[key].id;
                    } else {
                        request[key] = this.editedItem[key];
                    }
                }

                if (typeof request["role"] === "undefined") {
                    this.editedItem.role = this.current_role;
                }

                apiRoutes.updateuser(request);

                // Client side tricks
                this.editedItem.role = this.editedItem.role.name;

                if (typeof this.editedItem.role === "undefined") {
                    this.editedItem.role = this.current_role;
                }

                if (typeof this.editedItem.organization === "undefined") {
                    this.editedItem.organization = this.current_organization;
                }

                Object.assign(this.users[this.editedIndex], this.editedItem);
            } else {
                this.users.push(this.editedItem);
            }
            this.close();
        },
		
		invite: async function(){
			const response = await apiRoutes.inviteUser(this.invite_email);

			console.log(response);
		}
    }
};
</script>
