function checkAllowedAccess()
{
    const url = "api.php?operation=isAllowedAccess";
    const request = new Request(url, {
        method:'POST'
    });

    fetch(request)
        .then(request => request.json())
        .then(data =>
        {
            console.log(data);
            if(data.allowed)
            {
                showModal(false);
                showStats(true);
            }
            else
            {
                showModal(true);
                showNoContent(true,data.message);
            }
        });
}

function showStats(allow)
{
    const url = "api.php?operation=getStats";
    const request = new Request(url, {
        method:'POST',
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({allowed:allow,site:'C'}),
    });

    fetch(request)
        .then(request => request.json())
        .then(data =>
        {
            console.log(data);
            if(data.allowed)
            {
                if(data.correct)
                {
                    showNoContent(false,data.message);
                    getStats(data.stats);
                    showModal(false);
                }
                else
                {
                    showNoContent(true,data.message);
                }
            }
            else
            {
                showNoContent(true,data.message);
            }

        });
}


function showModal(show)
{
    if(show)
        $('#graphModal').modal('show');
    else
        $('#graphModal').modal('hide');
}

function closeModal()
{
    showModal(false);
}


function getContent(set)
{
    if(set)
    {
        showStats(true);
    }
}


function showNoContent(show,message) {
    let noContentArea = document.getElementById("noContentArea");
    let noContentText = document.getElementById("noContentText");

    let countryStats = document.getElementById("countryStats");
    let visitPerDay = document.getElementById("visitPerDay");
    let visitSites = document.getElementById("visitSites");
    let bestSite = document.getElementById("bestSite");
    let maps = document.getElementById("maps");


    if (show) {
        noContentArea.classList.remove("hidden");
        noContentText.innerText = message;
        countryStats.classList.add("hidden");
        visitPerDay.classList.add("hidden");
        visitSites.classList.add("hidden");
        bestSite.classList.add("hidden");
        maps.classList.add("hidden");
    } else {
        noContentArea.classList.add("hidden");
        noContentText.innerText = "";
        countryStats.classList.remove("hidden");
        visitPerDay.classList.remove("hidden");
        visitSites.classList.remove("hidden");
        bestSite.classList.remove("hidden");
        maps.classList.remove("hidden");
    }
}


function getStats(data)
{
    fillCountryStatsTable(data.countryStats);
    fillVisitPerDayTable(data.time);
    fillVisitTable(data.visits);
    fillBestSiteTable(data.bestSite);
    showMap(data.cords);

}

function fillCountryStatsTable(data)
{
    let bodyTable = document.getElementById("bodyTableCountryStats");
    bodyTable.innerHTML = "";

    data.forEach(d => {
        bodyTable.append(getCountryStatsRowTable(d));
    })

}

function fillVisitPerDayTable(data)
{

    let bodyTable = document.getElementById("bodyTableVisitPerDay");
    bodyTable.innerHTML = "";

    data.forEach(d => {
        bodyTable.append(getVisitPerDayRowTable(d));
    })

}

function fillVisitTable(data)
{
    let bodyTable = document.getElementById("bodyTableVisitSites");
    bodyTable.innerHTML = "";

    data.forEach(d => {
        bodyTable.append(getVisitSiteRowTable(d));
    })

}

function fillBestSiteTable(data)
{
    let bodyTable = document.getElementById("bodyTableBestSite");
    bodyTable.innerHTML = "";

    bodyTable.append(getBestSiteRowTable(data));
}

function showMap(data)
{
    var mymap = L.map('map').setView([48.6737532, 19.696058], 7);

    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoieHNva29sayIsImEiOiJja28wbjIwNjEwaGJkMnZtZmVtaGZ5Ymh0In0.z3fpnpkqUlUUNUkVJ7ptqA', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'your.mapbox.access.token'
    }).addTo(mymap);

    data.forEach(d => {
        L.marker([parseFloat(d.lat),parseFloat(d.lon)]).addTo(mymap);
    });

}




function getCountryStatsRowTable(data)
{
    let tr = document.createElement("tr");
    tr.append(getFlagCol(data.code));
    tr.append(getCountryCol(data.state));
    tr.append(getCol(data.count));
    return tr;
}

function getCol(text)
{
    let td = document.createElement("td");
    td.innerText = text;
    return td;
}

function getCountryCol(country)
{
    let td = document.createElement("td");
    td.innerText = country;

    td.style.cursor = "pointer";
    td.onclick = function (){
        showCityVisites(country);
    }
    return td;

}

function getFlagCol(flagCode)
{
    let url = "http://www.geonames.org/flags/x/";
    let type = ".gif";
    let fullUrl = url + flagCode.toLowerCase() + type;
    let td = document.createElement("td");

    let flagImage = document.createElement("img");
    flagImage.src = fullUrl;
    flagImage.classList.add("flag");

    td.append(flagImage);
    return td;

}



function getVisitPerDayRowTable(data)
{
    let tr = document.createElement("tr");
    tr.append(getCol(data.part));
    tr.append(getCol(data.count));

    return tr;
}


function getVisitSiteRowTable(data)
{
    let tr = document.createElement("tr");
    tr.append(getCol(data.site));
    tr.append(getCol(data.count));
    return tr;
}


function getBestSiteRowTable(data)
{
    let tr = document.createElement("tr");
    tr.append(getCol(data.site));
    tr.append(getCol(data.count));

    return tr;
}


function showCityVisites(country)
{
    const url = "api.php?operation=getCityCounts";
    const request = new Request(url, {
        method:'POST',
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({state:country}),
    });

    fetch(request)
        .then(request => request.json())
        .then(data =>
        {
            console.log(data);
            getCityData(data);
        });
}


function getCityRowTable(data)
{
    let tr = document.createElement("tr");
    tr.append(getCol(data.city));
    tr.append(getCol(data.count));

    return tr;
}

function getCityData(data)
{
    let bodyTable = document.getElementById("bodyTableCity");
    bodyTable.innerHTML = "";

    data.forEach(d => {
        bodyTable.append(getCityRowTable(d));
    });

    showCityModal(true);


}



function showCityModal(show)
{
    if(show)
        $('#cityModal').modal('show');
    else
        $('#cityModal').modal('hide');
}

function closeCityModal()
{
    showCityModal(false);
}