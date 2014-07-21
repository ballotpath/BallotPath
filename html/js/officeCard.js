// officeCard function
// Accepts an object as an argument.




function officeCard(cardData) {
	var levelString;
        var jsonString = JSON.stringify(cardData);
        console.log(jsonString);
	switch (cardData.level.toUpperCase()[0]) {
		case "F":
			levelString = "Federal";
			break;
		case "S":
			levelString = "State";
			break;
		case "C":
			levelString = "County";
			break;
		case "M":
			levelString = "Municipal";
			break;
		case "L":
			levelString = "Local";
			break;
	}
if (cardData.imgLink == "") {
        imgLink = "img/business_user.png";
} else {
        imgLink = cardData.imgLink;
}
var htmlString = '<!-- Begin Card --> \n' +
                 '<div class="office-card-outside animated ' + levelString + '" data-cardData=\'' + jsonString + '\'>\n' +
                 '    <div class="office-card-scope ' + levelString + '">           \n' +
                 '       <h4 class="office-card-title-text">' + levelString + '</h4> \n' +
                 '    </div>                                                        \n' +
                 '    <div class="office-card-title">                \n' +
                 '      <p class="office-card-title-text">' + cardData.title + '</p>\n' +
                 '    </div>                                                        \n' +
                 '    <div class="office-card-picture" style="background-image:url(\'' + imgLink + '\');">\n' +
                 '      <div class="office-card-term transparent">                 \n' +
                 '        <p class="office-card-term-text">' + cardData.term + ' Term</p>\n' +
                 '      </div>                                                        \n' +
                 '      <div class="office-card-dates transparent">                               \n' +
                 '        <p class="office-card-term-text">' + cardData.begin + '-' + cardData.end + '</p>\n' +
                 '      </div>                                                        \n' +
                 '      <div class="office-card-name transparent">                                \n' +
                 '        <h4>' + cardData.name + '</h4>\n' +
                 '      </div>                                                        \n' +
                 '    </div>\n' +
                 '</div> <!-- End Card --> \n';
return htmlString;
};

