<div class="">
    <article>
        <div class="content">$Content</div>
        <div class="row">
            <div class="form-group col-md-2 col-lg-4">
                <select name="event_location" id="event_location" class="form-control">
                    <option value="">All Locations</option>
                    <option value="Queenstown">Queenstown</option>
                    <option value="Wanaka">Wanaka</option>
                    <option value="Dunedin">Dunedin</option>
                </select>
            </div>
        </div>
        <p class="events-category-filter"> Filter: 
            <a href="#all"><span class="badge text-white p-2 bg-black" data-category="All">All </span></a>
            <% loop EventsCategories %>
            <a href="#$Title"><span class="badge text-white p-2" data-category="$Title" style="background-color:#$ColourStyle">$Title</span></a>
            <% end_loop %>
        </p>
        <% loop $GroupedEvents.GroupedBy(MonthYear) %>
            <h2 class="mt-10">$MonthYear</h2>
            <div class="row events-container">
                <% loop $Children %>
                    <div class="col-md-12 col-lg-6 event-item" data-groups='[<% loop EventCategory %>"$Title",<% end_loop %>"$Location"]'>
                        <div class="event $Location <% loop $EventCategory %>$Title <% end_loop %>"> 
                            <% include EventSummary %>
                        </div>
                    </div>

                <% end_loop %>
                <div class="col-md-2 col-lg-4 my-sizer-element" style="position: relative;z-index: -1;"></div>
            </div>
        <% end_loop %>
    </article>

    

</div>