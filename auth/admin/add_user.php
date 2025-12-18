
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption font-green" style="width: 100%;">
            <i class="icon-user-follow font-green"></i>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <span class="caption-subject bold uppercase">Add User</span>
        </div>
    </div>

    <form id="userCreate" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-md-line-input form-md-floating-label">

                        <input type="text" id="name" name="name" class="form-control input-sm" data-required="true"
                               value="">
                        <label for="name" class="control-label">Name</label>
                        <span class="form-control-focus"></span>
                    </div>

                    <div class="form-group form-md-line-input form-md-floating-label">

                        <input type="text" id="username" name="username" class="form-control input-sm"
                               data-required="true" value="">
                        <label for="username" class="control-label">Username</label>
                        <span class="form-control-focus"> </span>
                    </div>
                    <div class="form-group form-md-line-input form-md-floating-label">

                        <input type="email" id="email" name="email" class="form-control input-sm"
                               data-parsley-type="email" data-required="true"
                               value="">
                        <label for="email" class="control-label">Email</label>
                        <span class="form-control-focus"> </span>
                    </div>
                    <div class="form-group form-md-line-input form-md-floating-label">

                        <input type="text" id="password" name="password" class="form-control input-sm"
                               data-required="true" value="">
                        <label for="password" class="control-label">Password</label>
                        <span class="form-control-focus"> </span>
                    </div>


                    <div class="form-group form-md-radios">
                        <label for="status">Status</label>
                        <div class="form-md-radios md-radio-inline">
                            <div class="md-radio">
                                <input type="radio" id="radio14" name="status" class="md-radiobtn" value="enable">
                                <label for="radio14">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> Enable</label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" id="radio15" name="status" class="md-radiobtn" checked="" value="disable">
                                <label for="radio15">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> Disable </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-md-line-input form-md-radios">
                        <label for="emailVerified">Verified Email</label>
                        <div class="form-md-radios md-radio-inline ">
                            <div class="md-radio">
                                <input type="radio" id="radio16" name="emailVerified" class="md-radiobtn" value="yes">
                                <label for="radio16">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> Yes - Email is verified</label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" id="radio17" name="emailVerified" class="md-radiobtn" checked="" value="no">
                                <label for="radio17">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> No - Email is not verified </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group ">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                <img  src="http://www.placehold.it/200x150/EFEFEF/AAAAAA" alt="" /> </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                            <div>
                            <span class="btn green btn-file btn-outline">
                            <span class="fileinput-new"> Select image </span>
                            <span class="fileinput-exists"> Change </span>
                            <input type="file" name="image" id="image"> </span>
                                <a href="javascript:;" class="btn red fileinput-exists btn-outline" data-dismiss="fileinput"> Remove </a>
                            </div>
                        </div>
                        <div class="clearfix margin-top-10">
                            <span class="label label-danger">NOTE!</span> Image preview only works in IE10+, FF3.6+, Safari6.0+, Chrome6.0+ and Opera11.1+. In older browsers the filename is shown instead. </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
            <button type="submit" class="btn green btn-outline" name="submit" onclick="createUser();return false">Submit</button>
        </div>

    </form>
</div>