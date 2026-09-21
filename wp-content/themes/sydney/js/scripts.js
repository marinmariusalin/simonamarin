document.addEventListener('DOMContentLoaded', function () {
 var divElementsBookMarks = document.querySelectorAll("div[href]");
divElementsBookMarks.forEach(function(item){
	if(!item){
		return false;
	}
	var itemID = item.getAttribute('href');
	if(!itemID){
		return false;
	}
	item.addEventListener("click", function(e){
        let target = document.getElementById(item.getAttribute('href'));
		if(!target){
			return false;
		}
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        })
        e.preventDefault();
		return false;
	});
});
var divElementsBookMarks = document.querySelectorAll("div[open-link-href]");
divElementsBookMarks.forEach(function(item){
	if(!item){
		return false;
	}
	var target = item.getAttribute('open-link-href');
	if(!target){
		return false;
	}
	item.addEventListener("click", function(e){
       	window.open(target, "_self"); 
        e.preventDefault();
		return false;
	});
});
}, false);