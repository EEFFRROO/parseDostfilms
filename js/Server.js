class Server {

    constructor() {

    }

    async send(method, data) {
        const arr = [];
        for (let key in data) {
            arr.push(`${key}=${data[key]}`);
        }
        if (this.token) {
            arr.push(`&token=${this.token}`);
        }
        const response = await fetch(`api/?method=${method}&${arr.join('&')}`);
        const answer = await response.json();
        if (answer && answer.result === 'ok') {
            return answer.data;
        } else if(answer && answer.result === 'error') {
            return false;
        }
    }


    async getFilms() {
        return this.send("getFilms");
    }

    async getGenres() {
        return this.send("getGenres");
    }

    async getCountries() {
        return this.send("getCountries");
    }

    async getRatingsByGenre(genre) {
        return this.send("getRatingsByGenre", { genre });
    }

    async getStatByGenre(genre) {
        return this.send("getStatByGenre", { genre });
    }

    async getStatByCountry(country) {
        return this.send("getStatByCountry", { country });
    }

    async getBestFilmsByGenre(genre) {
        return this.send("getBestFilmsByGenre", { genre });
    }

    async getRandomFilm() {
        return this.send("getRandomFilm");
    }

    async refreshDBInfo() {
        return this.send("refreshDBInfo");
    }

}