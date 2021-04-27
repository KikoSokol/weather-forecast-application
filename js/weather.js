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
                showWeather(true);
            }
            else
            {
                showModal(true);
                showNoContent(true,data.message);
            }
        });
}


function showWeather(allow)
{
    const url = "api.php?operation=getWeather";
    const request = new Request(url, {
        method:'POST',
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({allowed:allow,site:'A'}),
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
                    getWeather(data.weather);
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
        showWeather(true);
    }
}





function getWeather(cityId)
{
    window.myWidgetParam ? window.myWidgetParam : window.myWidgetParam = [];
    window.myWidgetParam.push({
        id: 1,
        cityid: cityId,
        appid: 'd18770c402ea2c0fa2cbb1e16d2162c8',
        units: 'metric',containerid: 'openweathermap-widget-11',
    });

    (function()
    {
        var script = document.createElement('script');
        script.async = true;
        script.charset = "utf-8";
        script.src = "//openweathermap.org/themes/openweathermap/assets/vendor/owm/js/weather-widget-generator.js";
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(script, s);})();
}



function showNoContent(show,message)
{
    let noContentArea = document.getElementById("noContentArea");
    let noContentText = document.getElementById("noContentText");

    let weather = document.getElementById("weatherArea");


    if(show)
    {
        noContentArea.classList.remove("hidden");
        noContentText.innerText = message;
        weather.classList.add("hidden");
    }
    else
    {
        noContentArea.classList.add("hidden");
        noContentText.innerText = "";
        weather.classList.remove("hidden");
    }


}