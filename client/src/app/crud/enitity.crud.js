import axios from "axios";
const HOST = process.env.REACT_APP_SERVER_API_URL
export const LOGIN_URL = "api/auth/login";
export const REGISTER_URL = "api/auth/register";
export const REQUEST_PASSWORD_URL = "api/auth/forgot-password";
export const ENTITY = HOST;



export const ME_URL = "api/me";

export const entityList = async (currentPage) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    let headers = new Headers();
        ///headers.append('Access-Control-Allow-Origin', '*');
        headers.append('authorization', okta.idToken.idToken);
        headers.append('nonce', okta.idToken.claims.nonce);
        headers.append('sub', okta.idToken.claims.sub);
        headers.append('email', okta.idToken.claims.email);
    if(okta.idToken.idToken != undefined) {
        const response = await fetch(`${ENTITY}/entities?page=${currentPage}`, {
            method:'get',
            headers: headers
        });
        return Promise.resolve(response.json());
    }
}

export const checkAdmin = async (email) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta.idToken.idToken != undefined) {
        let headers = new Headers();
        ///headers.append('Access-Control-Allow-Origin', '*');
        headers.append('Authorization', okta.idToken.idToken);
        
        /* const response = await fetch(`${ENTITY}/players`,{
            method:'get',
            headers: headers,
            mode:'cors'
        }); */
        
        const response = await fetch(`${HOST}/check_user/${email}`,{
            method:'get',
            headers: headers
        });
        return Promise.resolve(response.json());
    }
}

export const entityDetail = async (zoho_id, email, entity) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        const response = await fetch(`${ENTITY}/entity_api/entity/?zoho_id=${zoho_id}&email=${email}&entity=${entity}`, {
            headers: {
                'Authorization': okta.idToken.idToken,

            }
        });
        return Promise.resolve(response.json());
    }
}
