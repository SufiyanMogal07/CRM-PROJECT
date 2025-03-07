import { BACKEND_URL } from "../../../config.js";

export const authToken = localStorage.getItem("authToken") || "";

export function decodedToken(jwt_decode) {
    try {
        return jwt_decode(authToken);
    } catch(error) {
        console.error(error);
        return "";
    }
}

export const defaultHeaders = {
    Authorization: `${authToken}`,
    "Content-Type": "application/json"
}

export async function getData(endpoint) {
    if(!endpoint) return {success: false,message: "Error! Endpoint is Missing"};
    try {
        const response = await fetch(BACKEND_URL + endpoint,
            {
                method: "GET",
                headers: defaultHeaders
            }
        );
        if(!response.ok) throw new Error("Server is Not Responding, Something Went Wrong");

        return await response.json();
    } catch (error) {
        console.error(error);
        return {success: false,message: "Something Went Wrong!!!"};
    }
}

export async function addData (endpoint,data) {
    if(!endpoint || !data) return {success: false,message: "Error! Endpoint and Data is Missing!!"}; 
    try {
        let response = await fetch(BACKEND_URL+endpoint,
            {
                method: 'POST',
                headers: defaultHeaders,
                body: JSON.stringify(data)
            }
        );
        if(!response.ok) throw new Error("Server is Not Responding, Something Went Wrong");
        return await response.json();
    } catch(error) {
        console.error(error);
        return {success: false,message: "Something Went Wrong!!!"};
    }
}

export async function updateData(endpoint,data) {
    if(!endpoint || !data) return {success: false,message: "Error! Endpoint and Data is Missing!!"}; 

    try {
        let response = await fetch(BACKEND_URL+endpoint,
            {
                method: "PATCH",
                headers: defaultHeaders,
                body: JSON.stringify(data)
            }
        )
        if(!response.ok) throw new Error("Server is Not Responding, Something Went Wrong");
    
        return await response.json();
    } catch(error) {
        console.error(error);
        return {success: false,message: "Something Went Wrong!!!"};
    }
   
}

export async function deleteData(endpoint) {
    if(!endpoint) return {success: false,message: "Error! Endpoint is Missing!!"}; 
    try {
        let response = await fetch(BACKEND_URL+endpoint,
            {
                method: "DELETE",
                headers: defaultHeaders,
            }
        );  
        if(!response.ok) throw new Error("Server is Not Responding, Something Went Wrong");

        return await response.json();
    } catch(error) {
        console.error(error);
        return {success: false,message: "Something Went Wrong!!!"};
    }
}