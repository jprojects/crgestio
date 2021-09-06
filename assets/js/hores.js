$.ajaxSetup({async: false});

document.addEventListener("DOMContentLoaded", function(event) {
    Mode();

    $('.Projecte').change(function()
    {
        var id   = $(this).val();
        var cell = $(this).closest('td').attr("class");
        console.log(cell);
        $.get(domain+"?view=hores&task=getDataProjecte&id="+id+"&mode=raw")
        .done(function( data ) {
            data = JSON.parse(data);
            $('.'+cell+' .Descripcio').val(data.razonsocial);
            $('.'+cell+' .HoresReals').val(data.totalhoresrecompte);
            $('.'+cell+' .HoresPrev').val(data.horesprevistes);
        });
    });

    $('#hores-form .remove').click(function()
    {
        var cell = $(this).data('cell');
        var id   = $('#hores-form .row'+cell+' > #Id').val();
        $.get(domain+"?view=hores&task=deleteHour&id="+id+"&mode=raw");
    });

    $('#itemsList .remove').click(function()
    {
        var cell = $(this).data('cell');
        var id   = $('#itemsList .row'+cell+' > #Id').val();
        $.get(domain+"?view=hores&task=deleteRegistre&id="+id+"&mode=raw");
    });

});


let classe = '';
let events = [];

//obtenim la data del localstoratge, si no hi ha data obtenim la actual
date = getDateLocalStorage();
annee = date.getFullYear();
moi = date.getMonth();

los_meses=["Gener","Febrer","Març","Abril","Maig","Juny","Juliol","Agost","Setembre","Octubre","Novembre","Desembre"];
los_meses2=["J","F","M","A","M","J","J","A","S","O","N","D"];
los_dias=["D","DIL","DIM","DIM","DIJ","DIV","DIS","DIU"];

function getEvents(firstDay, lastDay){

    let events = []

    //get Festius
    $.get(domain+"?view=hores&task=getFestius&mode=raw", {firstDay, lastDay})
        .done(function( data ) {
            data = JSON.parse(data);

            for (const i in data) {
                events.push({
                    date: data[i],
                    class: 'bg-festiu',
                });
            }
        }
    );

    //get Vacançes
    $.get(domain+"?view=hores&task=getVacances&mode=raw", {firstDay, lastDay})
        .done(function( data ) {
            data = JSON.parse(data);

            for (const i in data) {
                events.push({
                    date: data[i],
                    class: 'bg-vacança',
                });
            }
    });

    //get Baixes
    $.get(domain+"?view=hores&task=getBaixes&mode=raw", {firstDay, lastDay})
        .done(function( data ) {
            data = JSON.parse(data);

            for (const i in data) {
                events.push({
                    date: data[i],
                    class: 'bg-baixa',
                });
            }
    });

    //get Hores Treballades
    // $.get(domain+"?view=hores&task=getHoresTreballades&mode=raw", {firstDay, lastDay})
    //     .done(function( data ) {
    //         data = JSON.parse(data);

    //         for (const i in data) {
    //             events.push({
    //                 date: data[i],
    //                 class: 'blue',
    //             });
    //         }
    // });

    return events;
}

//format output date
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
}

function getDateLocalStorage(){

    return (localStorage.getItem('date')) ? new Date(localStorage.getItem('date')) : new Date();;
}

function getDateSelected(){
    let dateSelected = new Date();
    dateSelected.setYear(document.Calendrier.Annee.value);
    dateSelected.setMonth(document.Calendrier.Mois.value);
    dateSelected.setDate(1);

    return dateSelected;
}

function onDayClick(el) {
    rawDate = el.getAttribute('data-date')
    localStorage.setItem('date', rawDate);
    window.location.href = domain+'?view=hores&date='+rawDate; 
}

function Mois()
{
    //pintem els selects
    let htmlAnnee = "";
    let annee = date.getFullYear();
    for(i=(annee-20);i<(annee+21);i++){

        htmlAnnee += "<option ";
        if(i==annee) htmlAnnee += "selected ";
        htmlAnnee += "value='"+i+"'>"+i+"</option>";
    }
    $('#any').html(htmlAnnee);

    let htmlMois = "";
    let mois = date.getMonth();
    for(i=0;i<12;i++){

        htmlMois += "<option ";
        if(i==mois) htmlMois += "selected ";
        htmlMois += "value='"+i+"'>"+los_meses[i]+"</option>";
    }
    $('#mes').html(htmlMois);

    //obtenim el primer i ultim dia respecte el mes i any seleccionat
    const firstDay = formatDate(new Date(getDateSelected().getFullYear(), getDateSelected().getMonth(), 1));
    const lastDay = formatDate(new Date(getDateSelected().getFullYear(), getDateSelected().getMonth() + 1, 0));

    //fem les crides ajax a la base de dades
    events = getEvents(firstDay, lastDay);

    for(i=0;i<7;i++){
        calendrier+="<td width='"+100/7+"%'>"+los_dias[i+1]+"</td>";
    }

    calendrier+="</tr>";
    afficher.setYear(document.Calendrier.Annee.value);
    afficher.setMonth(document.Calendrier.Mois.value);
    afficher.setDate(31);

    if(afficher.getDate()!=31)afficher.setDate(31-afficher.getDate());

    afficher.setMonth(document.Calendrier.Mois.value);
    nbjours=afficher.getDate();
    nbsem=Sem(document.Calendrier.Annee.value,document.Calendrier.Mois.value,nbjours);
    nbsem=Sem(document.Calendrier.Annee.value,document.Calendrier.Mois.value,nbjours);
    nbsem-=Sem(document.Calendrier.Annee.value,document.Calendrier.Mois.value,1)-1;
    boutmois=new Date(document.Calendrier.Annee.value,document.Calendrier.Mois.value,nbjours);

    if(boutmois.getDay()==0){
        nbsem--;
    }

    boutmois=new Date(document.Calendrier.Annee.value,document.Calendrier.Mois.value,1);

    if(boutmois.getDay()==0){
        nbsem++;
    }

    h=1;
    for(i=0;i<nbsem;i++)
{
    calendrier+="<tr align='center'>";
        for(a=1;a<=7;a++,h++)
    {
        afficher.setDate(h);
        if((afficher.getDay()==a||(afficher.getDay()==0&&a==7))&&afficher.getMonth()==document.Calendrier.Mois.value)
        {
                Sem(document.Calendrier.Annee.value,document.Calendrier.Mois.value,h-1)
                Sem(document.Calendrier.Annee.value,document.Calendrier.Mois.value,h-1)

                if(afficher.getDay()==6) { //dissabte
                    classe = 'bg-saturday'; 
                }

                if(afficher.getDay()==0) { //diumenge
                    classe = 'bg-sunday'; 
                }
                
                //pintem els events
                for (let item of events) {
                    if(formatDate(afficher) == formatDate(item.date.fecha)){
                        classe = item.class;
                    }
                }

                //pintem el dia actual
                if(formatDate(afficher) == formatDate(getDateLocalStorage())){
                    classe = 'bg-actual';
                }

                calendrier+="<td class='bg-day "+classe+"' data-date='"+afficher.toISOString().split("T")[0]+"' onclick='onDayClick(this);'>";
                classe = '';
                calendrier+=afficher.getDate();
            }
            else
            {
                calendrier+="<td>&nbsp";
                h--;
            }
            calendrier+="</td>";
        }
        calendrier+="</tr>";
    }
    calendrier+="</table>";
    document.getElementById("Cal").innerHTML=calendrier;
    document.getElementById("SelMois").style.visibility="visible";
    
}

function Mode(r=false){
    //recuperem la data del select i la guardem al localsoratge, despres redirigim
    if(r) { 
        const rawDate = formatDate(getDateSelected());
        localStorage.setItem('date', rawDate);
        window.location.href = domain+'?view=hores&date='+rawDate; 
    }
    
    mode=1;
    calendrier="<table style='font-size:9' width='100%' border='1'>";
    calendrier+="<tr align='center' bgcolor='#00FFFF'>";
    afficher=new Date();
    Mois(); 
}

function Sem(A,M,K){

    date.setYear(A);
    date.setMonth(M);
    date.setDate(K);
    date2=new Date(A,0,1);
    x=1;

    do
    {
        date2.setDate(x);
        x++;
    }
    while(date2.getDay()!=1);

    temps=date.getTime()-date2.getTime()+24*60*60*1000;
    sem=temps/(1000*60*60*24*7);

    return Math.ceil(sem);
}





