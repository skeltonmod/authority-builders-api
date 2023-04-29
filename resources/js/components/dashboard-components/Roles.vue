<template>
	<v-app id="inspire">
		<Nav />
		<v-card class="mx-auto pa-4 ma-12" width="80%">
			<v-data-table :headers="headers" :items="roles" class="elevation-1">
				<template v-slot:top>
					<v-toolbar flat>
						<v-spacer></v-spacer>
						<v-dialog v-model="dialog" max-width="500px">
							<template v-slot:activator="{ on, attrs }">
								<v-btn
									color="primary"
									dark
									class="mb-2"
									v-bind="attrs"
									v-on="on"
								>
									New Item
								</v-btn>
							</template>
							<v-card>
								<v-card-title>
									<span class="text-h5">{{ formTitle }}</span>
								</v-card-title>
								<v-card-text>
									<v-container>
										<v-row>
											<v-col cols="12" sm="6" md="12">
												<v-text-field
													v-model="editedItem.name"
													label="Name"
												></v-text-field>
											</v-col>
											<v-col
												cols="12"
												sm="7"
												md="4"
												hidden
											>
												<v-text-field
													v-model="editedItem.id"
													label="ID"
													readonly
												></v-text-field>
											</v-col>
											<v-col cols="12" sm="6" md="12">
												<v-combobox
													v-model="role_perms_model"
													:items="role_perms_items"
													:search-input.sync="search"
													hide-selected
													hint="Add Permissions"
													label="Add some Permissions"
													multiple
													persistent-hint
													small-chips
												></v-combobox>
											</v-col>
											<v-col cols="12" sm="7" md="4">
												<v-text-field
													v-model="editedItem.guard"
													label="Guard"
													readonly
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
										@click="save"
									>
										Save
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
	// TODO: Refactor
	data: () => ({
		dialog: false,
		dialogDelete: false,
		headers: [
			{
				text: "Name",
				align: "start",
				sortable: false,
				value: "name",
			},
			{ text: "Guard", value: "guard" },
			{ text: "Actions", value: "actions", sortable: false },
		],
		roles: [],
		role_perms_model: [], // Fill this with current perms
		role_perms_items: [], // Fill this with perms you don't own
		current_role: "",
		search: null,
		editedIndex: -1,
		editedItem: {
			id: 0,
			name: "",
			guard: "",
			permissions: [],
		},
		defaultItem: {
			name: "",
			guard: "api",
		},
	}),
	computed: {
		formTitle() {
			return this.editedIndex === -1 ? "New Item" : "Edit Item";
		},
	},

	watch: {
		dialog(val) {
			val || this.close();
		},
		dialogDelete(val) {
			val || this.closeDelete();
		},
		role_perms_model(val){
			// delete chip on select
			this.$nextTick(() => this.role_perms_model.pop);
		}
	},

	created() {
		this.initialize();
	},

	methods: {
		initialize: async function() {
			const roles = await apiRoutes.getroles();
			const perms = await apiRoutes.getpermissions();
			// Use only one temporary array
			let temp_array = [];
			roles.forEach(function(value, index) {
				temp_array.push({
					id: value.id,
					name: value.name,
					guard: value.guard_name,
				});
			});
			this.roles = temp_array;

			temp_array = [];
			perms.forEach(function(value, index) {
				temp_array.push(value.name);
			});

			this.role_perms_items = temp_array;
		},

		// AJAX Request here
		editItem: async function(item){
			this.editedIndex = this.roles.indexOf(item);
			this.editedItem = Object.assign({}, item);
			this.dialog = true;

			// Get the permission of a role
			const role_perm = await apiRoutes.getroleperms(this.editedItem.id);
			let temp_array = [];
			role_perm.forEach(function(value, index){
				temp_array.push(value.name);
			});
			this.role_perms_model = temp_array;
		},

		deleteItem(item) {
			this.editedIndex = this.roles.indexOf(item);
			this.editedItem = Object.assign({}, item);
			this.dialogDelete = true;
		},

		deleteItemConfirm() {
			this.roles.splice(this.editedIndex, 1);
			this.closeDelete();
		},

		close() {
			this.dialog = false;
			this.$nextTick(() => {
				this.editedItem = Object.assign({}, this.defaultItem);
				this.editedIndex = -1;
			});
		},

		closeDelete() {
			this.dialogDelete = false;
			this.$nextTick(() => {
				this.editedItem = Object.assign({}, this.defaultItem);
				this.editedIndex = -1;
			});
		},

		save() {
			if (this.editedIndex > -1) {
				apiRoutes.editrole(this.editedItem.name, this.editedItem.id, this.role_perms_model);
				Object.assign(this.roles[this.editedIndex], this.editedItem);
			} else {
				// Add
				apiRoutes.createrole(this.editedItem.name);
				this.roles.push(this.editedItem);
			}
			this.close();
		},
	},
};
</script>
