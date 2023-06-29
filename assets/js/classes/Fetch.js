class Fetch {
    static async send(url, method = 'GET', headers = {'Content-Type': 'application/json'}, data = {}) {
        return await fetch(url, {
            method: method,
            headers: headers,
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .catch(error => console.log(error));
    }


    static async get(url) {
        return await fetch(url)
            .then(response => response.json())
            .catch(error => console.log(error));
    }
}

export default Fetch;