
var map;

google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
    var hash = getVars();
    var map_canvas = document.getElementById('map_canvas');
    var map_options = {
        center: new google.maps.LatLng( 45, -122), //hash['lat'], hash['lng']),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(map_canvas, map_options);

    var ctaLayer = new google.maps.KmlLayer({
      url: kml,
      suppressInfoWindows: true
    });

    ctaLayer.setMap(map);

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
            var out = data.office.title;
            $resultsArea.html(out);
            
            $resultsArea = $('#reqSal');
            out = '<dt>Age Requirement</dt><dd>';
			if(data.office.age_requirements) out += data.office.age_requirements; 
			else out += '<br>';
            out += ' </dd><dt>Residency Requirements</dt><dd>';
			if(data.office.res_requirements) out += data.office.res_requirements; 
			else out += '<br>';
            out += '</dd><dt>Professional Requirements</dt><dd>';
			if(data.office.prof_requirements) out += data.office.prof_requirements; 
			else out += '<br>';			
            out += '</dd><dt>Salary</dt><dd>'; 
			if(data.office.salary) out += '$' + data.office.salary; 
			else out += '<br>';	
			out +=  '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#timeFra');
            out = '<dt>Duration of Office</dt><dd>'; 
			if(data.office.term_length_months) out += data.office.term_length_months + ' Months'; 
			else out += '<br>';	
            out += '</dd><dt>Next Election</dt><dd>'; 
			if(data.office_positions[0].next_election) out += data.office_positions[0].next_election; 
			else out += '<br>';
            out += '</dd><dt>Filing Deadline</dt><dd>'; 
			if(data.office_positions[0].filing_deadline) out += data.office_positions[0].filing_deadline; 
			else out += '<br>';
			out += '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#basDut');
            out = '<dt>Responsibilities</dt><dd>'; 
			if(data.office.responsibilities) out += data.office.responsibilities; 
			else out += '<br>';
            out += '</dd><dd>';
			if(data.office.notes) out += data.office.notes; 
			out += '</dd>';
            $resultsArea.html(out);
            
            $resultsArea = $('#filDoc');
            out =  '';
            var oDocs = data.office_docs
            for (var i = 0; i < oDocs.length; ++i) {
                out += '<a href="' + oDocs[i].link + '" target="_blank" >' + oDocs[i].name + '</a><br>';
            }
            var eDocs = data.office_positions[0].election_div_docs
            for (var i = 0; i < eDocs.length; ++i) {
                out += '<a href="' + eDocs[i].link + '"  target="_blank">' + eDocs[i].name + '</a><br>';
            }
            if (out == '') {
                out = '<dt>""</dt><dd>""</dd>';
            }
            $resultsArea.html(out);
            
            $resultsArea = $('#wheHow');
            out = '<dt>District</dt><dd>' + data.office_positions[0].district.name + '</dd>';
            out += '<dt>Election Division</dt><dd>' + data.office_positions[0].election_div.name + '</dd>';
			
            out += '<dt>Physical Address</dt><dd>';
	    physaddr = '';
			if(data.office_positions[0].election_div.phys_addr_addr1) physaddr += data.office_positions[0].election_div.phys_addr_addr1; 
            physaddr += '</dd><dd>';
			if(data.office_positions[0].election_div.phys_addr_addr2) physaddr += data.office_positions[0].election_div.phys_addr_addr2; 
	    physaddr += '</dd><dd>' ;
			if(data.office_positions[0].election_div.phys_addr_city) physaddr += data.office_positions[0].election_div.phys_addr_city; 
            physaddr += '</dd><dd>';
			if(data.office_positions[0].election_div.phys_addr_state) physaddr += data.office_positions[0].election_div.phys_addr_state; 
	    physaddr += '</dd><dd>';
			if(data.office_positions[0].election_div.phys_addr_zip) physaddr += data.office_positions[0].election_div.phys_addr_zip; 
	    out += physaddr;			

            out += '</dd><dt>Mailing Address</dt><dd>'; 
			if(data.office_positions[0].election_div.mail_addr_addr1) out += data.office_positions[0].election_div.mail_addr_addr1;
			else out += physaddr;
            out += '</dd><dd>';
			if(data.office_positions[0].election_div.mail_addr_addr2) out += data.office_positions[0].election_div.mail_addr_addr2; 
			out += '</dd><dd>' ;
			if(data.office_positions[0].election_div.mail_addr_city) out += data.office_positions[0].election_div.mail_addr_city; 
            out += '</dd><dd>';
			if(data.office_positions[0].election_div.mail_addr_state) out += data.office_positions[0].election_div.mail_addr_state; 
			out += '</dd><dd>';
			if(data.office_positions[0].election_div.mail_addr_zip) out += data.office_positions[0].election_div.mail_addr_zip; 
			
            out += '<dt>Phone Number</dt><dd>'; 
			if(data.office_positions[0].election_div.phone) out += data.office_positions[0].election_div.phone; 
			else out += '<br>';
			out += '</dd><dt>Website</dt><dd>' ;
			if(data.office_positions[0].election_div.website) out += data.office_positions[0].election_div.website; 
			else out += '<br>';
            out += '</dd><dt>Filling Fee</dt><dd>'; 
			if(data.office.filing_fee) out += data.office.filing_fee; 
			else out += '<br>';
			out += '</dd>';
            $resultsArea.html(out);
        }
    });
});

