window.onload = () => {
    const server = new Server();

    const test = document.getElementById("test");
    const showGenresBtn = document.getElementById("showGenres");
    const showCountriesBtn = document.getElementById("showCountries");
    const tempBtn = document.getElementById("temp");
    const compareGenresBtn = document.getElementById("compareGenresBtn");
    const compareCountriesBtn = document.getElementById("compareCountriesBtn");
    const actionField = document.getElementById("actionField");
    const showBestFilmsBtn = document.getElementById("showBestFilmsBtn");
    const showRandomFilmBtn = document.getElementById("showRandomFilmBtn");
    const refreshDBInfo = document.getElementById("refreshDBInfo");

    // test.onclick = async () => {
    //     deleteOld("testTable");
    //     let data = await server.getFilms();
    //     // console.log(data);
    //     let columns = ["Название", "Страна", "Длительность", "Озвучка", "Дата выхода", "Оригинальное название", "Режиссёр", "Актёры", "Рейтинг", "Жанр"];
    //     let table = createTable(data, columns);
    //     table.id = "testTable";
    //     document.getElementById("actionField").append(table);
    // }

    // showGenresBtn.onclick = () => {createSelectGenres("selectGenres")};
    // showCountriesBtn.onclick = () => {createSelectCountries("selectCountries")};
    // tempBtn.onclick = async () => {
    //     console.log(await server.getStat("Комедии"));
    // }

    refreshDBInfo.onclick = async () => {
        actionField.innerHTML = "";
        let h2 = document.createElement("h2");
        h2.innerText = "Идёт обновление.";
        let loading = setInterval(() => {
            if (h2.innerText == "Идёт обновление...")
                h2.innerText = "Идёт обновление";
            else
                h2.innerText += ".";
        }, 300);
        actionField.append(h2);
        let prom = server.refreshDBInfo();
        prom.then(() => {clearInterval(loading)});
        // console.log(prom);
        let res = await prom;
        h2.innerText = res;
    }

    showRandomFilmBtn.onclick = async () => {
        actionField.innerHTML = "";
        let data = await server.getRandomFilm();
        let columns = ["Название", "Страна", "Длительность", "Озвучка", "Дата выхода", "Оригинальное название", "Режиссёр", "Актёры", "Рейтинг", "Жанр"];
        deleteOld("randomFilm");
        let table = createTable(data, columns);
        table.id = "randomFilm";
        actionField.append(table);
    }

    showBestFilmsBtn.onclick = async () => {
        actionField.innerHTML = "";
        let selectField = await createSelectGenres("selectGenresForShow");
        actionField.append(selectField);
        deleteOld("confirmShowGenresBtn");
        let confirmShow = document.createElement("button");
        confirmShow.id = "confirmShowGenresBtn";
        confirmShow.innerText = "Показать";
        confirmShow.onclick = async () => {
            let data = await server.getBestFilmsByGenre(selectField.value);
            deleteOld("bestFilms");
            let columns = ["Название", "Страна", "Длительность", "Озвучка", "Дата выхода", "Оригинальное название", "Режиссёр", "Актёры", "Рейтинг", "Жанр"];
            let table = createTable(data, columns);
            table.id = "bestFilms";
            actionField.append(table);
        }
        actionField.append(confirmShow);
    }

    compareGenresBtn.onclick = async () => {
        actionField.innerHTML = "";
        let firstSelectField = await createSelectGenres("firstSelectGenres");
        let secondSelectField = await createSelectGenres("secondSelectGenres");
        actionField.append(firstSelectField);
        actionField.append(secondSelectField);
        deleteOld("confirmCompareGenresBtn");
        let compareBtn = document.createElement("button");
        compareBtn.innerText = "Сравнить";
        compareBtn.id = "confirmCompareGenresBtn";
        compareBtn.onclick = async () => {
            let firstData = await server.getStatByGenre(firstSelectField.value); // Статистика по первому жанру
            let secondData = await server.getStatByGenre(secondSelectField.value); // Статистика по второму жанру
            deleteOld("comparedGenres");
            let div = document.createElement("div");
            div.id = "comparedGenres";
            let stat = outputStat(firstData, secondData);
            stat.className = "comparedInfo";
            div.append(stat);
            // stat = outputStat(secondData);
            // stat.className = "genreInfo";
            // div.append(stat);
            // div.style.whiteSpace = "nowrap";
            actionField.append(div);
        }
        actionField.append(compareBtn);
    }

    compareCountriesBtn.onclick = async () => {
        actionField.innerHTML = "";
        let firstSelectField = await createSelectCountries("firstSelectCountries");
        let secondSelectField = await createSelectCountries("secondSelectCountries");
        actionField.append(firstSelectField);
        actionField.append(secondSelectField);
        deleteOld("confirmCompareCountriesBtn");
        let compareBtn = document.createElement("button");
        compareBtn.innerText = "Сравнить";
        compareBtn.id = "confirmCompareCountriesBtn";
        compareBtn.onclick = async () => {
            let firstData = await server.getStatByCountry(firstSelectField.value); // Статистика по первому жанру
            let secondData = await server.getStatByCountry(secondSelectField.value); // Статистика по второму жанру
            deleteOld("comparedCountries");
            let div = document.createElement("div");
            div.id = "comparedCountries";
            let stat = outputStat(firstData, secondData);
            stat.className = "comparedInfo";
            div.append(stat);
            // stat = outputStat(secondData);
            // stat.className = "genreInfo";
            // div.append(stat);
            // div.style.whiteSpace = "nowrap";
            actionField.append(div);
        }
        actionField.append(compareBtn);
    }


    function outputStat(firstData, secondData) {
        let div = document.createElement("div");
        let text = document.createElement("p");
        let newFirstData = [];
        let newSecondData = [];
        let marks = [];
        for (key in firstData) {
            firstData[key] = firstData[key].toString();
            secondData[key] = secondData[key].toString();
            firstData[key] = firstData[key].replace(",", ".");
            secondData[key] = secondData[key].replace(",", ".");
            firstData[key] = parseFloat(firstData[key]);
            secondData[key] = parseFloat(secondData[key]);
            newFirstData.push(firstData[key]);
            newSecondData.push(secondData[key]);
            if (firstData[key] > secondData[key]) {
                marks.push(">");
            } else if (firstData[key] == secondData[key]) {
                marks.push("=");
            } else {
                marks.push("<");
            }
        }
        let tempSpace = "&nbsp;&nbsp;&nbsp;";
        let thirdSpace = tempSpace + tempSpace + tempSpace;
        text.innerHTML = "Ср.арифм: " + newFirstData[0] + thirdSpace + marks[0] + thirdSpace + newSecondData[0] + "<br>";
        text.innerHTML += "Мода: " + thirdSpace + newFirstData[1] + thirdSpace + marks[1] + thirdSpace + newSecondData[1] + "<br>";
        text.innerHTML += "Медиана: " + tempSpace + newFirstData[2] + thirdSpace + marks[2] + thirdSpace + newSecondData[2] + "<br>";
        div.append(text);
        return div;
    }

    
    // Создание таблицы по данным и названиям столбцов
    function createTable(data, columns) {
        let table = document.createElement("table"); // Таблица
        table.style.border = "1px solid"
        // Добавление строки с названиями столбцов
        let tr = document.createElement("tr"); // Строка
        columns.forEach(i => {
            th = document.createElement("th"); // Ячейка названия столбца
            th.innerText = i; // Текст ячейки
            tr.append(th); // Добавление ячейки в строку
        });
        table.append(tr); // Добавление строки в таблицу
        // Добавление данных в таблицу
        if (data) {
            data.forEach(i => {
                tr = document.createElement("tr"); // Строка таблицы
                for (let j in i) {
                    td = document.createElement("td"); // Ячейка строки таблицы
                    td.innerText = i[j]; // Текст ячейки
                    tr.append(td); // Добавление ячейки в строку
                }
                // i.forEach(j => {
                //     td = document.createElement("td"); // Ячейка строки таблицы
                //     td.innerText = j; // Текст ячейки
                //     tr.append(td); // Добавление ячейки в строку
                // }); 
                table.append(tr); // Добавление строки в таблицу
            });
            return table;
        }
        return false;
    }
    function createSelectField(data) { // Создание поля выбора
        let select = document.createElement('select');
        // data.forEach(i => {
        //     let option = document.createElement('option');
        //     option.innerText = i;
        //     option.value = i;
        //     select.append(option);
        // });
        for (key in data) {
            for (i in data[key]) {
                let option = document.createElement('option');
                option.innerText = data[key][i];
                option.value = data[key][i];
                select.append(option);
            }
        }
        return select;
    }
    function deleteOld(id) {
        let temp = document.getElementById(id);
        if (temp)
            temp.remove();
    }
    async function createSelectGenres(id) {
        deleteOld(id);
        let genres = await server.getGenres();
        // console.log(genres);
        let selectField = createSelectField(genres);
        selectField.id = id;
        return selectField;
        // document.getElementById("actionField").append(selectField);
    }
    async function createSelectCountries(id) {
        deleteOld("selectCountries");
        let countries = await server.getCountries();
        // console.log(genres);
        let selectField = createSelectField(countries);
        selectField.id = id;
        return selectField;
        // document.getElementById("actionField").append(selectField);
    }
}