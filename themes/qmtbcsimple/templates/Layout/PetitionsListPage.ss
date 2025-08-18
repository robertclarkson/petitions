<div class="content-container unit size3of4 lastUnit">
    <article>
        <div class="content">$Content</div>
        <% loop Petitions %>
        <div class="row petition-wrapper">
                    <div class="col-12 col-sm-4 petition-image p-0" style="background-image: url('$Image.ScaleWidth(800).Url')">
                <a href="$Link">
                </a>
                    </div>
                <div class="col-12 col-sm-8 petition-content">
                    <h2><a href="$Link">$Title</a></h2>
                    <h4>Closing: $ClosingDate.Nice ($ClosingDate.Ago)</h4>
                    <h4>Confirmed Signatures: $Submissions.filter('Verified', 1).Count</h4>
                    <a class="btn btn-primary" href="$Link">Sign This Submission</a>
                    <p></p>
                </div>
        </div>

        <% end_loop %>
    </article>

    

</div>