<template>
	<v-app id="inspire">
		<Nav/>
		<v-card
			class="mx-auto pa-4 ma-12" width="80%"
		>
		<v-data-table
				:headers="headers"
				:items="organizations"
				class="elevation-1"
			>
				<template v-slot:top>
					<v-toolbar flat>
						<v-spacer></v-spacer>
						<v-dialog v-model="dialog" max-width="250px">
							<template v-slot:activator="{ on, attrs }">
								<v-btn
									color="primary"
									dark
									class="mb-2"
									v-bind="attrs"
									v-on="on"
								>
									New Permission
								</v-btn>
							</template>
							<v-card>
								<v-card-title>
									<span class="text-h5">{{ formTitle }}</span>
								</v-card-title>
								<v-card-text>
									<v-container>
										<v-row>
											<v-col cols="16" sm="6" md="12">
												<v-text-field
													v-model="editedItem.name"
													label="Name"
												></v-text-field>
											</v-col>
											<v-col cols="12" sm="7" md="4" 
													hidden>
												<v-text-field
													v-model="editedItem.id"
													label="ID"
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
			{ text: "Actions", value: "actions", sortable: false },
		],
		organizations: [],
		editedIndex: -1,
		editedItem: {
			id: 0,
			name: "",
			guard: "",
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
	},

	created() {
		this.initialize();
	},

	methods: {
		initialize: async function() {
			const organizations = await apiRoutes.getorganizations();
			// Use only one temporary array
			let temp_array = [];
			organizations.forEach(function(value, index) {
				temp_array.push({
					id: value.id,
					name: value.name,
				});
			});

			this.organizations = temp_array;
		},

		editItem(item) {
			this.editedIndex = this.organizations.indexOf(item);
			this.editedItem = Object.assign({}, item);
			this.dialog = true;

			
		},

		deleteItem(item) {
			this.editedIndex = this.organizations.indexOf(item);
			this.editedItem = Object.assign({}, item);
			this.dialogDelete = true;
		},

		deleteItemConfirm() {
			this.organizations.splice(this.editedIndex, 1);
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
				apiRoutes.updatepermission(this.editedItem.name, this.editedItem.id);
				Object.assign(this.organizations[this.editedIndex], this.editedItem);

			} else {
				// Add
				apiRoutes.createorganization(this.editedItem.name);
				this.organizations.push(this.editedItem);
			}
			this.close();
		},
	}
}
</script>