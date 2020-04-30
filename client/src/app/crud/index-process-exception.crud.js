import axios from "axios";
const HOST = process.env.REACT_APP_SERVER_API_URL
export const LOGIN_URL = "api/auth/login";
export const REGISTER_URL = "api/auth/register";
export const REQUEST_PASSWORD_URL = "api/auth/forgot-password";
export const ENTITY = HOST;



export const ME_URL = "api/me";

export const indexProcessList = async (currentPage) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));
    let headers = new Headers();
        ///headers.append('Access-Control-Allow-Origin', '*');
        headers.append('authorization', okta.idToken.idToken);
        headers.append('nonce', okta.idToken.claims.nonce);
        headers.append('sub', okta.idToken.claims.sub);
        headers.append('email', okta.idToken.claims.email);
    if(okta.idToken.idToken != undefined) {
        const response = await fetch(`${ENTITY}/index_exceptions?page=${currentPage}`, {
            method:'get',
            headers: headers
        });
        return Promise.resolve(response.json());
    }
}
export const indexExceptionDetail = async (indexProcessID) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        let headers = new Headers();
        ///headers.append('Access-Control-Allow-Origin', '*');
        headers.append('authorization', okta.idToken.idToken);
        headers.append('nonce', okta.idToken.claims.nonce);
        headers.append('sub', okta.idToken.claims.sub);
        headers.append('email', okta.idToken.claims.email);
        const response = await fetch(`${ENTITY}/index_exception/${indexProcessID}`, {
            headers: headers,
        });
        return Promise.resolve(response.json());
    }
}

export const reIndexFile = async (indexProcessID, entityName) => {
    const okta = await JSON.parse(localStorage.getItem('okta-token-storage'));

    if(okta) {
        let headers = new Headers();
        ///headers.append('Access-Control-Allow-Origin', '*');
        headers.append('authorization', okta.idToken.idToken);
        headers.append('nonce', okta.idToken.claims.nonce);
        headers.append('Content-Type', 'application/x-www-form-urlencoded');
        headers.append('sub', okta.idToken.claims.sub);
        headers.append('email', okta.idToken.claims.email);
        const response = await fetch(`${ENTITY}/reindex_file`, {
            headers:headers,
            method:'post',
            body: `id=${indexProcessID}&name=${entityName}`
        });
        return Promise.resolve(response.json());
    }
}


