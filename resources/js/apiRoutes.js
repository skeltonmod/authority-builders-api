import APIRequest from "./APIRequest";
import Cookie from "js-cookie";
import axios from "axios";
const routes = {
	// API Calls
	login: async (email, password) => {
		return await APIRequest({
			url: "api/login",
			data: {
				email: email,
				password: password,
			},
			method: "POST",
		}).then((r) => {
			return r.data;
		});
	},

	logout: async () => {
		return await APIRequest({
			url: "api/logout",
			method: "POST",
		}).then((r) => {
			
			return r.data;
		});
	},

	register: async (email, password, name, invited) => {
		return await APIRequest({
			url: "api/register",
			data: {
				email: email,
				password: password,
				name: name,
				invited: invited
			},
			method: "POST",
		}).then((r) => {
			return r.data;
		});
	},

	getusers: async () => {
		return await APIRequest({
			url: "api/getusers",
			method: "GET",
		}).then((r) => {
			return r.data;
		});
	},

	getaccount: async () => {
		return await APIRequest({
			url: "api/getaccount",
			method: "GET",
		}).then((r) => {
			return r.data;
		});
	},

	getroles: async () =>{
		return await APIRequest({
			url: "api/getroles",
			method: "GET"
		}).then((r)=> {
			return r.data;
		});
	},

	getroleperms: async (id) =>{
		return await APIRequest({
			url: "api/getroleperms",
			data:{
				id: id
			},
			method: "POST"
		}).then((r)=> {
			return r.data;
		});
	},

	getpermissions: async () =>{
		return await APIRequest({
			url: "api/getpermissions",
			method: "GET"
		}).then((r)=> {
			return r.data;
		});
	},

	createpermission: async (name) =>{
		return await APIRequest({
			url: "api/createpermissions",
			data:{
				name: name
			},
			method: "POST"
		}).then((r)=> {
			return r.data;
		});
	},

	updatepermission: async (name, id) =>{
		return await APIRequest({
			url: "api/updatepermission",
			data:{
				name: name,
				id: id
			},
			method: "POST"
		}).then((r)=> {
			return r.data;
		});
	},

	editrole: async (name, id, permissions) =>{
		return await APIRequest({
			url: "api/updaterole",
			data:{
				name: name,
				id: id,
				permissions: permissions
			},
			method: "POST"
		}).then((r)=> {
			return r.data;
		});
	},

	createrole: async (name) =>{
		return await APIRequest({
			url: "api/createrole",
			data:{
				name: name
			},
			method: "POST"
		}).then((r)=> {
			return r.data;
		});
	},

	updateaccount: async (data) => {
		return await APIRequest({
			url: "api/updateaccount",
			data,
			method: "POST",
		}).then((r) => {
			return r.data;
		});
	},

	updateuser: async(data) => {
		return await APIRequest({
			url: "api/updateuser",
			data,
			method: "POST"
		}).then((r) => {
			return r.data
		});
	},

	updatelocation: async(data) => {
		return await APIRequest({
			url: "api/updatelocation",
			data,
			method: "POST"
		}).then((r) => {
			return r.data
		});
	},

	getlocation: async() => {
		return await APIRequest({
			url: "api/getlocation",
			method: "GET"
		}).then((r) => {
			return r.data
		});
	},

	getorganizations: async() =>{
		return await APIRequest({
			url: "api/getorganizations",
			method: "GET"
		}).then((r) => {
			return r.data
		});
	},

	getorganizations: async() =>{
		return await APIRequest({
			url: "api/getorganizations",
			method: "GET"
		}).then((r) => {
			return r.data
		});
	},

	createorganization: async(name) => {
		return await APIRequest({
			url: "api/createorganization",
			data:{
				name: name
			},
			method: "POST"
		}).then((r)=> {
			return r.data;
		});
	},

	updatepermission: async(name, id) => {
		return await APIRequest({
			url: "api/updateorganization",
			data:{
				name: name,
				id: id
			},
			method: "POST"
		}).then((r)=> {
			return r.data;
		});
	},

	saveConfig: async(data) => {
		return await APIRequest({
			url: "api/saveconfig",
			data,
			method: "POST"
		}).then((r) => {
			return r.data
		})
	},

	getTimezones: async ()=>{
		return await APIRequest({
			url: "api/getimezone",
			method: "GET"
		}).then((r) => {
			return r.data
		});
	},

	socialAuth: async(provider, data) =>{
		return await APIRequest({
			url: `api/socialauth/${provider}`,
			data,
			method: "POST"
		}).then((r) => {
			return r.data
		});
	},

	inviteUser: async(email) => {
		return await APIRequest({
			url: 'api/inviteuser',
			data:{
				email: email
			},
			method: "POST"
		}). then((r) => {
			return r.data
		});
	}

};

export default routes;
