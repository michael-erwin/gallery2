<div class="row">
    <div class="col-sm-6 col-lg-3">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-image"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Uploaded Photos</span> <span class="info-box-number"><?php echo @$photos_total;?></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-film"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Uploaded Videos</span> <span class="info-box-number"><?php echo @$videos_total;?></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-user"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Registered Users</span> <span class="info-box-number"><?php echo @$users_count;?></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-pie-chart"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Unique Visitors</span> <span class="info-box-number"><?php echo @$unique_visits;?></span>
            </div>
        </div>
    </div>
</div>
