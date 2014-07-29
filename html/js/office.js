
var map;

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
    var hash = getVars();
    var map_canvas = document.getElementById('map_canvas');
    var map_options = {
        center: new google.maps.LatLng( 45, -122); //hash['lat'], hash['lng']),
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

$(document).ready(function() {
    var hash = getVars();
    var requestURL="http://ec2-54-213-36-220.us-west-2.compute.amazonaws.com/api/office/" + hash['id'];
    var $resultsArea = $('#position-title');
    $.jsonp({
        url: requestURL,
        success: function(data) {
            console.log(data);
            var out = data.office[0].title;
            $resultsArea.html(out);
            
            $resultsArea = $('#reqSal');
            out = '<dt>Age Requirement</dt><dd>' + data.office[0].age_requirements + '</dd>';
            out += '<dt>Residency Requirements</dt><dd>' + data.office[0].res_requirements + '</dd>';
            out += '<dt>Professional Requirements</dt><dd>' + data.office[0].prof_requirements + '</dd>';
            out += '<dt>Salary</dt><dd>' + data.office[0].salary + '</dd>';
            out += '<dd>' + data.office[0].office_notes + '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#timeFra');
            out = '<dt>Duration of Office</dt><dd>' + data.office[0].term_lenght_months + ' Months</dd>';
            out += '<dt>Next Election</dt><dd>' + data.office[0].next_election + '</dd>';
            out += '<dt>Filing Deadline</dt><dd>' + data.office[0].filing_deadline + '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#basDut');
            out = '<dt>Responsibilities</dt><dd>' + data.office[0].responsibilities + ' Months</dd>';
            out += '<dd>' + data.office[0].position_notes + '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#filDoc');
            out = '<dt>'  +  data.office[0].office_doc_name + '</dt><dd>' + data.office[0].office_doc_link + '</dd>';
            out += '<dt>'  +  data.office[0].div_doc_name + '</dt><dd>' + data.office[0].div_doc_link + '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#wheHow');
            out = '<dt>District</dt><dd>' + data.office[0].district + '</dd>';
            out += '<dt>Election Division</dt><dd>' + data.office[0].election_division + '</dd>';
            out += '<dt>Physical Address</dt><dd>' + data.office[0].phys_addr_addr1 + '</dd>' ;
            out += '<dd>' + data.office[0].phys_addr_addr2 + '</dd><dd>' + data.office[0].phys_addr_city + '</dd>';
            out += '<dd>' + data.office[0].phys_addr_state + '</dd><dd>' + data.office[0].phys_addr_zip + '</dd>';
            out += '<dt>Mail Address</dt><dd>' + data.office[0].mail_addr_addr1 + '</dd>';
            out +='<dd>' + data.office[0].mail_addr_addr2 + '</dd><dd>' + data.office[0].mail_addr_city + '</dd>';
            out += '<dd>' + data.office[0].mail_addr_state + '</dd><dd>' + data.office[0].mail_addr_zip + '</dd>';
            out += '<dt>Phone Number</dt><dd>' + data.office[0].phone + '</dd>';
            out += '<dt>Website</dt><dd>' + data.office[0].website + '</dd>';
            out += '<dt>Filling Fee</dt><dd>' + data.office[0].filing_fee + '</dd>';
            $resultsArea.html(out);
    });
});

