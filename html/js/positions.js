
var map;

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
    var hash = getVars();
    var map_canvas = document.getElementById('map_canvas');
    var map_options = {
        center: new google.maps.LatLng(hash['lat'], hash['lng']),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(map_canvas, map_options);
}

function getVars() {
        var query = $.url().attr('query');
        var args = query.split("&");
        var hash = {};
        for (var i = 0; i < args.length; ++i ) {
                var set = args[i].split("=");
                hash[set[0]] = set[1];
        }
        return hash;
}

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
    if (cardData.photo_link == "") {
        photo_link = "img/business_user.png";
    } else {
        photo_link = cardData.photo_link;
    }
    var htmlString = '<!-- Begin Card --> \n' +
                 '<div class="office-card-outside animated ' + levelString + '" data-cardData=\'' + jsonString + '\'>\n' +
                 '    <div class="office-card-scope ' + levelString + '">           \n' +
                 '       <h4 class="office-card-title-text">' + levelString + '</h4> \n' +
                 '    </div>                                                        \n' +
                 '    <div class="office-card-title">                \n' +
                 '      <p class="office-card-title-text">' + cardData.position_name + '</p>\n' +
                 '    </div>                                                        \n' +
                 '    <div class="office-card-picture" style="background-image:url(\'' + photo_link + '\');">\n' +
                 '      <div class="office-card-term transparent">                 \n' +
                 '        <p class="office-card-term-text">' + cardData.term + ' Term</p>\n' +
                 '      </div>                                                        \n' +
                 '      <div class="office-card-dates transparent">                               \n' +
                 '        <p class="office-card-term-text">' + cardData.begin + '-' + cardData.end + '</p>\n' +
                 '      </div>                                                        \n' +
                 '      <div class="office-card-name transparent">                                \n' +
                 '        <h4>' + cardData.first_name + " " + cardData.last_name + '</h4>\n' +
                 '      </div>                                                        \n' +
                 '    </div>\n' +
                 '</div> <!-- End Card --> \n';
    return htmlString;
};

function openOffice(office_id) {
    var url = window.location.protocol + "//" + window.location.host + "/office.html" + "?id=" + office_id;
    window.open(url, "_self");
}

$(document).ready(function(){
        var hash = getVars();

        $("#hideFederal").click(function() {
                 $(this).parent().toggleClass("active ");
                $(".Federal").parent().toggle('show');
        });

        $("#federal-check-box").change(function() {
                 $(this).toggleClass('active');
                $(".Federal").parent().toggle('show');
        });
        $("#state-check-box").change(function() {
                $(".State").parent().toggle('show');
        });
        $("#county-check-box").change(function() {
                $(".County").parent().toggle('show');
        });
        $("#city-check-box").change(function() {
                $(".Municipal").parent().toggle('show');
        });
        $("#local-check-box").change(function() {
                $(".Local").parent().toggle('show');
        });

// Make all the cards
        var requestURL="http://ec2-54-213-36-220.us-west-2.compute.amazonaws.com/api/office/" + hash['lat'] + "/" + hash['lng'];
        var $resultsArea = $('#cards');
        $.jsonp({
                url: requestURL,
                success: function(data) {
                    console.log(data);              
                    console.log(data.positions);
			var positionMatrix = "";
                    if (data.positions && data.positions.length > 0) {
                        $.each(data.positions, function(i, card) {
                            dyn_id = card.office_id;
                            positionMatrix += '<div class="col-lg-2 col-md-3 col-sm-4 card-cell" id=' + dyn_id + ' onclick="openOffice(' + dyn_id + ')" >\n';
                            positionMatrix += officeCard(card);
                            positionMatrix += '</div>\n';
                        });
                        $resultsArea.html(positionMatrix);
                    }
                    else {
                        $resultsArea.html('<p>No positions found for this distict. Please try again.</p>');
                    }

                // Pop-up Effect as mouse hovers over cards:
                // This has to happen AFTER the cards are loaded because they are bound to this
                // function.
                        var moveLeft = 100;
                        var moveDown = 30;

                        $('.office-card-outside').hover(function(e) {
                                var positionInfo = $( this ).data('carddata');
                                $('div#popuptext').empty();
				if(positionInfo.office_notes) {
                                	$('div#popuptext').append('<p>Bio: ' + positionInfo.office_notes + '</p>');
				}
				else {
					$('div#popuptext').append('<p>Bio not available </p>');
				}
                        $('div#pop-up').show()
                        .css('top', e.pageY + moveDown)
                        .css('left', e.pageX + moveLeft)
                        .appendTo('body');
                        }, function() {
                        $('div#pop-up').hide();
                        });

                        $('.office-card-outside').mousemove(function(e) {
                        $("div#pop-up").css('top', e.pageY + moveDown).css('left', e.pageX - moveLeft);
                        });
                // End of pop-up effect
                }
        });
});

