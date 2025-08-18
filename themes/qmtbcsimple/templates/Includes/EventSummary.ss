<div> 
    <div class="relative event-summary__image" style="height:350px; background-position: center; background-image: url('$Image.ScaleWidth(800).Url')">
        <div class="h-100">
            <div style="height:65px;" class="absolute bottom-0 w-100 overlay bg-opacity-40 bg-black p-3 text-white overflow-hidden">
                <div class="float-right">
                    <% loop $EventCategory %>
                    <span class="badge" style="background-color:#$ColourStyle">$Title</span>
                    <% end_loop %>
                    <div>
                        <span class="badge" style="background-color:#000">$Location</span>
                    </div>
                </div>
                <h2 class="event-summary__title"><a class="text-xl uppercase font-bold text-white" href="$Link">$Title</a></h2>
                <div class="summary">
                    $Summary
                    <p>
                        $MemberPrice.Nice<br />
                        <a class="text-white" href="$Link">Sign me up &rarr;</a><br />
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="$EventCategory.First.ColourStyle text-white p-3" style="background-color:#$EventCategory.First.ColourStyle">
        <h4 class="text-2xl uppercase font-bold m-0">$EventDateTime.format('E'), <span class="text-4xl">$EventDateTime.format('d')</span> $EventDateTime.format('MMM YYYY')</h4>
    </div>
</div>