<div class="services_sidebar">
    <h2 class="par-h2 mb-4">Our Services</h2>

    <ul style="list-style: none; padding: 0; align-items: right;">
        <li><a href="/personal_tax.php" style="border-radius: 10px 0px 0px;">Personal Income Tax</a></li>
        <li><a href="/corporate_tax.php">Corporate Income Tax</a></li>
        <li><a href="/incorporate.php" style="border-radius: 0px 0px 0px 10px;">Incorporate / Register a Business</a></li>
        <li><a href="/bookkeeping.php" style="border-radius: 0px 10px 0px 0px;">Accounting / Bookkeeping</a></li>
        <li><a href="/payroll_salary.php">Payroll &amp; Salary Calculations</a></li>
        <li><a href="/gst_hst.php" style="border-radius: 0px 0px 10px 0px;">GST/HST Returns</a></li>
    </ul>
</div>

<form method="POST" class="side_form" action="/sidebar_sendmail.php">

    <p class="form_sidebar_title">FREE CONSULTATION</p>

    <div class="col-md-12">
        <input type="text" name="name" placeholder="First Name" class="form-control" id="validationCustom01">
    </div>

    <div class="col-md-12">
        <input name="last_name" type="text" placeholder="Last Name" class="form-control" id="validationCustom01">
    </div>

    <div class="col-md-12">
        <input name="email" type="text" placeholder="Email" class="form-control" id="validationCustom01">

    </div>

    <div class="col-md-12">
        <input name="subject" type="text" placeholder="Subject" class="form-control" id="validationCustom01">

    </div>

    <div class="col-md-12">
        <textarea name="message" style="height: 110px;" placeholder="Message" class="form-control" id="validationCustom01"></textarea>
    </div>

    <p style="text-align: center;">
        <input type="submit" id="sidebar_submit" value="Send Message" />
    </p>
</form>