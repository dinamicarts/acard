$.ajaxSetup({
  type: 'POST'
});


function ShowError(strErr, obj){
	$(obj).after("<ul class='errors'><li>" + strErr + "</li></ul>");
}
function RemoveErrors(obj){
	$(obj).next("ul.errors").remove();
}

function ShowCheckAcardMessage(jsonResp){
	
	RemoveErrors($("#acard"));
	RemoveErrors($("#cognomenome"));
	
	var rJSon = eval ("(" + jsonResp + ")");
	if(rJSon.acard!="OK") {
		ShowError("Il numero di a/card non &egrave; stato trovato nei nostri database.", $("#acard"));
		return;
	}
	
	if(rJSon.ragsoc!="OK"){
		ShowError("Non c'&egrave; corrispondenza tra il numero di a/card inserito ed i campi cognome e nome.", $("#cognomenome"));
	}
}


function ShowCheckUserMessage(resp){
	RemoveErrors($("#email"));
	if(resp!="OK")
		ShowError("Questa email &egrave; gi&agrave; associata ad un account.", $("#email"));
}

function CheckACard(nACard, ragioneSociale, callback){
	$.ajax({
		url: baseUrl + "/server/check/",
		data: {'acard':nACard, 'ragsoc': ragioneSociale},
		success:function(ris){
			callback(ris);
		},
		dataType: 'text'
	});
}

function CheckEmail(email, callback){
	$.ajax({
		url: baseUrl + "/server/checkuser/",
		data: {'email':email},
		success:function(ris){
			callback(ris);
		},
		dataType: 'text'
	});
}

