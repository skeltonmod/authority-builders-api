// App level Cookie
import Cookie from "js-cookie";
import APIRequest from "./APIRequest";

export async function isAuthenticated() {
	window.axios.defaults.headers.common[
		"Authorization"
	] = `Bearer ${Cookie.get("auth_token")}`;
	return await APIRequest({
		url: "api/checkauth",
		method: "POST",
	})
		.then((r) => {
			return true;
		})
		.catch(function(error) {
			if (error.response) {
				if (error.response.status === 401) {
					return false;
				}
			}
		});
}
const store = {
	routeGuard: async (to, from, next) => {
		const authenticated = await isAuthenticated();

		if (to.name !== "Login") {
			
			if (authenticated) {
				next();
			} else {
				next({
					name: "Login",
					query: { redirectFrom: to.fullPath },
				});
				Cookie.remove("auth_token");
			}
		} else if (to.name === "Login") {
			if (authenticated) {
				next({ name: "Home" });
			} else {
				next();
			}
		}
	
	},
};

export default store;
