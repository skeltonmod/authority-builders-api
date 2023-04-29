<template>
	<div>
		<v-app id="inspire" >
			<v-card class="mx-auto overflow-hidden rounded-0 " height="100%" width="100%">
				<Nav/>


				<v-card-text class="d-flex justify-center">
					The navigation drawer will appear from the bottom on smaller
					size screens.
				</v-card-text>
			</v-card>
		</v-app>
	</div>
</template>

<script>
import Nav from "./Nav.vue";
export default {
	data: () => ({
		
	}),
	components: { Nav },
	mounted() {
		console.log("Home component mounted");
	},
	methods: {
		
	},
};
</script>
