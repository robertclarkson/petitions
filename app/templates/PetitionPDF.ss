<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style>
	body {
        margin:0px;
		font-family: 'DejaVu Sans', sans-serif;
	}
    @page { 
        margin-left: 10px;
        margin-bottom: 20px;
	   margin-right: 10px;
    }
    .invoice-box {
        max-width: 1000px;
        margin: auto;
        padding: 30px;
        font-size: 12px;
        line-height: 16px;
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        text-align: left;
    }
    
    .invoice-box table td,
    .invoice-box table th {
        padding: 5px 10px;
        vertical-align: top;
    }
    
    .invoice-box table tr.top td:nth-child(2),
    .invoice-box table tr.information td:nth-child(2),
    .invoice-box table tr.heading th:nth-child(4),
    .invoice-box table tr.item td:nth-child(4),
    .invoice-box table tr.total td:nth-child(3) {
        text-align: right;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    
    .invoice-box table tr.heading th {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td.bold {
        border-top: 2px solid #eee;
        font-weight: bold;
    }
    
	h1 {
		text-align: center;
	}

    table td, table th {
    	text-align: left;
    }

    footer {
        position: absolute; 
        bottom: 20px; 
        left: 20px; 
        right: 0px;
        height: 35px; 
        line-height: 35px;
        text-align: center;
        /** Extra personal styles **/
    }
    </style>
</head>
	<body>
		<div class="invoice-box">
			<h1>My personal submission on the Proposed Sunshine Coast Planning Scheme</h1>
			<h2>To: Sunshine Coast Council</h2>

			<h2>My Details</h2>
			<table cellpadding="0" cellspacing="0">
                <tr>
                	<th>
                		Name
                	</th>
                    <td>
                    	$Data.Name
                    </td>
                </tr>
                <tr>
                	<th>
                		Age Range
                	</th>
                    <td>
                    	$Data.AgeRange
                    </td>
                </tr>
                <tr>
                	<th>
                		Phone
                	</th>
                    <td>
                    	$Data.Phone
                    </td>
                </tr>
                <tr>
                	<th>
                		Email
                	</th>
                    <td>
                    	$Data.Email
                    </td>
                </tr>
                <tr>
                	<th>
                		Address
                	</th>
                    <td>
                    	$Data.AddressLine1, $Data.AddressLine2
                    </td>
                </tr>
                <tr>
                    <th>
                        Suburb
                    </th>
                    <td>
                        $Data.Suburb
                    </td>
                </tr>
                <tr>
                    <th>
                        City
                    </th>
                    <td>
                        $Data.City
                    </td>
                </tr>
                <tr>
                	<th>
                		Postcode
                	</th>
                    <td>
                    	$Data.Postcode
                    </td>
                </tr>
                <tr>
                	<th>
                		Country
                	</th>
                    <td>
                    	$Data.FullCountry
                    </td>
                </tr>
	        </table>

            <h2>Grounds for making submission</h2>
            <ul>
                <% if $Data.Resident %><li>I am a resident of the Sunshine Coast</li><% end_if %>
                <% if $Data.Business %><li>I do business on the Sunshine Coast</li><% end_if %>
                <% if $Data.Work %><li>I work on the Sunshine Coast</li><% end_if %>
                <% if $Data.Recreation %><li>I recreate on the Sunshine Coast</li><% end_if %>
                <% if $Data.Other %><li>$Data.Other</li><% end_if %>
            </ul>

            <h2>My submission is</h2>
			<p>$Data.MySubmissionIs</p>
           
            <% if $Data.Beerwah == 'oppose' %>
                <p><strong>Beerwah East SEQ Development Area:</strong> I strongly oppose the proposal to develop this area and strongly insist that this area is preserved in perpetuity as undeveloped natural space, revegetated with native plants and used for the community’s recreation and natural environment. This aligns with our Vision for the coast, and themes in the proposed planning scheme. I absolutely oppose continuing the urban sprawl consuming the coast and this would exacerbate that problem.</p>
            <% else_if $Data.Beerwah == 'support' %>
                <p><strong>Beerwah East SEQ Development Area:</strong> I strongly support the proposed development in this area.</p>
            <% end_if %>

            <% if $Data.Revegetation == 'oppose' %>
                <p><strong>Revegetation and reforestation:</strong> I strongly oppose setting firm targets for reforestation and revegetation of our native forests and bushland across the Coast.</p>
            <% else_if $Data.Revegetation == 'support' %>
                <p><strong>Revegetation and reforestation:</strong> I strongly support setting firm targets in the order of 70% of land area being regenerated into native forest and bushland cover across the Sunshine Coast region, with this target to be reflected directly in and facilitated by the Planning Scheme, as a priority. This should form the basis of our Planning Scheme and the questions of how we manage the remaining 30% area to facilitate housing, agriculture & forestry, technology & commerce, infrastructure, educational faciltiies, cleared recreation areas and other uses can then be addressed off the back of that. The proposed plan includes some great vision statements such as 'the most sustainable region in Australia' and 'the region’s outstanding biodiversity, natural assets and landscapes, including the Blackall Range and Glass House Mountains, beaches, headlands, coastal plains, waterways and wetlands are protected and enhanced and remain undiminished by development.' however it monumentally fails to deliver on that promise. In fact it does the opposite, and this is completely unacceptable.</p>
            <% end_if %>

            <% if $Data.Sustainability == 'oppose' %>
                <p><strong>Sustainability:</strong> I strongly oppose setting clear definitions around sustainability and genuinely ensuring the plan delivers to the sustainability Vision it claims.</p>
            <% else_if $Data.Sustainability == 'support' %>
                <p><strong>Sustainability:</strong> I strongly support setting clear definitions of sustainability, and to ensure the plan literally delivers the Vision of being the most sustainable region in Australia. I note that ongoing 'growth', development and bulldozing of our limited land area is, by definition, not sustainable! I propose we allow further growth or development once we have demonstrated we can be fully sustainable at our current scale of development and population. Sustainability will not get easier by cramming more people in, it will be harder and I do not accept 'greenwashing' of a plan that does not deliver true sustainability.</p>
            <% end_if %>
            
            <% if $Data.Traffic == 'oppose' %>
                <p><strong>Traffic & Transport:</strong> I strongly oppose continued development and densification across the Coast due to the already existing traffic congestion and parking issues. I strongly object to ever-widening and more congeted roads, bringing us closer and closer to what we see in other areas of SE Qld and eroding our quality of life. This is a fundamental consideration in our quality of life and is already unacceptably poor, with any further development compounding the issues or requiring ever larger transport infrastructure, which is directly at odds with the natural character of the Coast. </p>
            <% else_if $Data.Traffic == 'support' %>
                <p><strong>Traffic & Transport:</strong> I strongly support continued development, increased population density and the traffic and parking congestion and larger roads that will inevitably come with this.</p>
            <% end_if %>
            
            <% if $Data.Mooloolah == 'oppose' %>
                <p><strong>Mooloolah River Catchment Area:</strong> I strongly oppose any further development of the Mooloolah River catchment area and strongly request that the native vegetation is expanded substantially and protected across both the north and south banks of the river (from around Brightwater and the Sunshine Coast Hospital, through to Palmview), and south through Meridan Plains. This river is unique and special within our Council area. The National Park should be extended across publicly owned lands and zoning of the other areas should retain it's rural character. All efforts should be made to expand the natrual habitat around this fragile ecosystem which only has a small slither remaining and has seen ever encroaching development. There is also potential here for open green space and recreation, however we should ensure this retains its natural character. I do not want this area developed with the typical developer-designed 'green space'.</p>
            <% else_if $Data.Mooloolah == 'support' %>
                <p><strong>Mooloolah River Catchment Area:</strong> I strongly support development of the Mooloolah River catchment area. I want to see ongoing development encroaching the National Park and consuming the historic farmlands on both the north and south banks of the river.</p>
            <% end_if %>
            
            <% if $Data.MaroochydoreHeight == 'oppose' %>
                <p><strong>Maroochydore - unlimited height development:</strong> I strongly oppose the unlimited height development zoning the State Government has forced upon us and our community. This is at odds with the nature of and our Vision for the Coast, and I ask that the Sunshine Coast Council and our elected members pursue this with State Government to correct their poorly considered zoning. We can innovate and create a prosperous economy and healthy community without this.</p>
            <% else_if $Data.MaroochydoreHeight == 'support' %>
                <p><strong>Maroochydore - unlimited height development:</strong> I strongly support the unlimited height development in zoning Maroochydore.</p>
            <% end_if %>
            
            <% if $Data.Caloundra == 'oppose' %>
                <p><strong>Densification from Caloundra to Kawana:</strong> I strongly oppose rezoning of the various sections between Calounda and Kawana to increase density and drive unit development. This is absolutely out of character with this area and our ‘community of communities’ ethos on the Sunshine Coast.</p>
            <% else_if $Data.Caloundra == 'support' %>
                <p><strong>Densification from Caloundra to Kawana:</strong> I strongly support the proposed densification from Caloundra to Kawana.</p>
            <% end_if %>
            
            <% if $Data.Recreation == 'oppose' %>
                <p><strong>Recreation areas and open spaces:</strong> I strongly oppose any further loss of outdoor recreation spaces, facilities or amenities our communities value. In many cases, open mindedness and respect for our various community groups, sub-cultures and history would result in much better community outcomes. I request that the use of public land be made available to reestablish any lost facilities or space for the various groups who call this place home and who will be the custodians of this public land into the future.</p>
            <% else_if $Data.Recreation == 'support' %>
                <p><strong>Recreation areas and open spaces:</strong> I strongly support the ongoing loss of outdoor recreation spaces, facilities and amenities in order to accommodate development.</p>
            <% end_if %>
            
            <% if $Data.MaroochydoreDevelopment == 'oppose' %>
                <p><strong>Maroochydore development:</strong> I strongly oppose the changes to increased heights and densities in Maroochydore. This further exacerbates the already problematic traffic and parking, creates further congestion and spillover effects. Modest development may be acceptable, however this plan goes much too far, and also proposes rezoning outside of the CBD which I strongly object to.</p>
            <% else_if $Data.MaroochydoreDevelopment == 'support' %>
                <p><strong>Maroochydore development:</strong> I strongly support the proposed increases to heights and densities in Maroochydore.</p>
            <% end_if %>
            
            <% if $Data.Alexandra == 'oppose' %>
                <p><strong>Alexandra Headland Development:</strong> I strongly oppose the changes to any increased heights and densities in Alexandra Headland, including the area off Mari Street. This further exacerbates the already problematic traffic and parking, creates further congestion and spillover effects and continues to erode the character of the Alexandra Headland area.</p>
            <% else_if $Data.Alexandra == 'support' %>
                <p><strong>Alexandra Headland Development:</strong> I strongly support the proposed increases to heights and densities in Alexandra Headland.</p>
            <% end_if %>

	        <h2>Signature</h2>
			<img src="data:image/jpg;base64,$Signature" />
			
			<p>$Data.Name</p>
			<p>Date: $Data.Created.Format('{o} MMMM Y')</p>
		</div>
	</body>
</html>