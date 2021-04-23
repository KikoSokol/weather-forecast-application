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
                showIpInfo(true);
            }
            else
            {
                showModal(true);
                showNoContent(true,data.message);
            }
        });
}


function showIpInfo(allow)
{
    const url = "api.php?operation=getIpInfo";
    const request = new Request(url, {
        method:'POST',
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({allowed:allow}),
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
                    getIpInfo(data);
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



function getIpInfo(data)
{
    clearIpInfo();
    document.getElementById("ipAddress").innerText = data.ipInfo.host.ipAddress;
    document.getElementById("lat").innerText = data.ipInfo.location.latitude;
    document.getElementById("lon").innerText = data.ipInfo.location.longitude;
    document.getElementById("city").innerText = data.ipInfo.location.city;
    document.getElementById("state").innerText = data.ipInfo.location.state;
    document.getElementById("capitalCity").innerText = data.ipInfo.location.capitalCity;

}

function clearIpInfo()
{
    document.getElementById("ipAddress").innerText = "";
    document.getElementById("lat").innerText = "";
    document.getElementById("lon").innerText = "";
    document.getElementById("city").innerText = "";
    document.getElementById("state").innerText = "";
    document.getElementById("capitalCity").innerText = "";
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
        showIpInfo(true);
    }
}



function showNoContent(show,message)
{
    let noContentArea = document.getElementById("noContentArea");
    let noContentText = document.getElementById("noContentText");

    let ipInfo = document.getElementById("ipInfoArea");


    if(show)
    {
        noContentArea.classList.remove("hidden");
        noContentText.innerText = message;
        ipInfo.classList.add("hidden");
        clearIpInfo();
    }
    else
    {
        noContentArea.classList.add("hidden");
        noContentText.innerText = "";
        ipInfo.classList.remove("hidden");
    }


}