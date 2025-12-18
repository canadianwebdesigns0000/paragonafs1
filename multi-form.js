(function ($) {
	$.fn.multiStepForm = function (args) {
		if (args === null || typeof args !== 'object' || $.isArray(args))
			throw ' : Called with Invalid argument'
		var form = this
		var tabs = form.find('.tab')
		var steps = form.find('.step')


		steps.each(function (i, e) {
			$(e).on('click', function (ev) {

				if (curIndex() !== i) {

					if ('validations' in args && typeof args.validations === 'object' && !$.isArray(args.validations)) {

						if (i > 0) {
							form.validate(args.validations)

							if (form.valid()) {

								if (i === 2) {
									//alert('1');
									form.navigateTo(1)

									if (form.valid()) {

										// $('.CustomTabs ul.tabs li:eq(0) a').click();

										// Check if the current step is the second step and the "Enter Personal Information" tab is active
										if ($('#personInfo.step').hasClass('active') || $('#docUploadInfo.step').hasClass('active')) {
											// AJAX update when clicking "Next" in the second step with the "Enter Personal Information" tab active
											const formData = form.serialize();

											$('.CustomTabs ul.tabs li:eq(0) a').click();

											// console.log(formData)
											$.ajax({
												url: "../update_personal_information.php",
												type: "POST",
												data: formData,
												success: function (response) {
													// Handle success response
													$(".form_append_error").empty();

													var responseData = JSON.parse(response);

													$('#applicant_name_tab').text(responseData.firstname + " Documents");
													// Check if responseData.spouse_firstname is empty

													if ($('#applicant_spouse_tab').parent().hasClass('current')) {
														$('.applicant_spouse_tab_item').show(); // Hide the tab content item
													}

													if (responseData.spouse_firstname === '' || responseData.spouse_file_tax === 'No' || responseData.residing_canada === 'No') {
														$('#applicant_spouse_tab').parent().hide(); // Hide the tab anchor
														$('.applicant_spouse_tab_item').hide(); // Hide the tab content item
													} else {
														$('#applicant_spouse_tab').text(responseData.spouse_firstname + " Documents");
														$('#applicant_spouse_tab').parent().show(); // Show the tab anchor
													}

													// Function to get the value of the checked radio button by name
													function getCheckedRadioValue(name) {
														var radio = document.querySelector('input[name="' + name + '"]:checked');
														return radio ? radio.value : null;
													}

													var firstFillingTax = getCheckedRadioValue("first_fillingtax");

													if (firstFillingTax === 'Yes') {
														$('#upload_sin_number_document').show();
													} else {
														$('#upload_sin_number_document').hide();
													}

													var spouseFirstFillingTax = getCheckedRadioValue("spouse_first_tax");

													if (spouseFirstFillingTax === 'Yes') {
														$('#spouse_upload_sin_number_document').show();
													} else {
														$('#spouse_upload_sin_number_document').hide();
													}

												},
												error: function (response) {
													// Handle error response
													console.error(response);
												},
											});

											$('form input').each(function () {
												var inputName = $(this).attr('name'); // Get the name of the input field
												var inputValue = $(this).val(); // Get the value of the input field

												// Check if the input field has a name and a value
												if (inputName && inputValue) {
													// Check if the input field is a radio button
													if ($(this).attr('type') === 'radio') {
														// Only log the input field if it is checked
														if ($(this).is(':checked')) {
															console.log('Input field "' + inputName + '" has value: ' + inputValue);
															var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
															$.ajax({
																url: '../log_inputs.php',
																type: 'post',
																data: { 'inputName': inputName, 'inputValue': inputValue, 'email': email },
															});
														}
													} else {
														// Log the name and value of the input field
														console.log('Input field "' + inputName + '" has value: ' + inputValue);
														var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
														$.ajax({
															url: '../log_inputs.php',
															type: 'post',
															data: { 'inputName': inputName, 'inputValue': inputValue, 'email': email },
														});
													}
												}
											});
										}
										form.navigateTo(i)
									} else {
										createAlert(
											'Please fill in all required fields.',
											'',
											'',
											'danger',
											true,
											true,
											'pageMessages'
										)
										// setTimeout(function () {
										// 	$('.alert').alert('close')
										// }, 5000)
										// console.log("Form 1 is Invalid");
										var dangerAlert = `
											<div class="alert alert-danger alert-dismissible bg-danger border-danger text-white alert-label-icon shadow fade show" role="alert">
												<i class="ri-error-warning-line label-icon"></i><strong>Please Fill In All Required Fields</strong>
												<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
											</div>
										`;
										$(".form_append_error").empty().append(dangerAlert);
										var errors = $(".error").get().reverse(); // Get all elements with class 'error' and reverse their order
										var errorNames = new Set(); // Create a new Set to store the names of the input fields

										errors.forEach(function (element) {
											$(element).focus(); // Focus on each element
											if (element.name) { // Check if the name property is not undefined
												errorNames.add(element.name); // Add the name of each element to the Set
											}
										});

										// Convert the Set back to an Array
										var uniqueErrorNames = Array.from(errorNames);

										uniqueErrorNames.forEach(function (name) {
											console.log(name); // Log the name of each unique element
											var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
											$.ajax({
												url: '../log_errors.php',
												type: 'post',
												data: { 'error': name, 'email': email },
												success: function (data) {
													console.log("Error logged successfully");
												}
											});
										});
									}
								}
							} else {
								if (i === 2) {
									//alert('2');
									createAlert(
										'Please fill in all required fields.',
										'',
										'',
										'danger',
										true,
										true,
										'pageMessages'
									)
									// setTimeout(function () {
									// 	$('.alert').alert('close')
									// }, 5000)
									var dangerAlert = `
										<div class="alert alert-danger alert-dismissible bg-danger border-danger text-white alert-label-icon shadow fade show" role="alert">
											<i class="ri-error-warning-line label-icon"></i><strong>Please Fill In All Required Fields</strong>
											<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
										</div>
									`;
									$(".form_append_error").empty().append(dangerAlert);
									var errors = $(".error").get().reverse(); // Get all elements with class 'error' and reverse their order
									var errorNames = new Set(); // Create a new Set to store the names of the input fields

									errors.forEach(function (element) {
										$(element).focus(); // Focus on each element
										if (element.name) { // Check if the name property is not undefined
											errorNames.add(element.name); // Add the name of each element to the Set
										}
									});

									// Convert the Set back to an Array
									var uniqueErrorNames = Array.from(errorNames);

									uniqueErrorNames.forEach(function (name) {
										console.log(name); // Log the name of each unique element
										var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
										$.ajax({
											url: '../log_errors.php',
											type: 'post',
											data: { 'error': name, 'email': email },
											success: function (data) {
												console.log("Error logged successfully");
											}
										});
									});
								}
							}

						} else {
							form.navigateTo(0)
						}

						if (i === 1) {
							form.navigateTo(i)
						}

					} else {
						// form.navigateTo(2)
					}
				}
			})
		})


		form.navigateTo = function (i) {
			/*index*/
			/*Mark the current section with the class 'current'*/
			tabs.removeClass('current').eq(i).addClass('current')
			// Show only the navigation buttons that make sense for the current section:
			form.find('.previous').toggle(i > 0)
			atTheEnd = i >= tabs.length - 1
			form.find('.next').toggle(!atTheEnd)
			// console.log('atTheEnd='+atTheEnd);
			form.find('.submit').toggle(atTheEnd)
			fixStepIndicator(curIndex())
			return form
		}


		function curIndex() {
			/*Return the current index by looking at which section has the class 'current'*/
			return tabs.index(tabs.filter('.current'))
		}


		function fixStepIndicator(n) {
			steps.each(function (i, e) {
				if (i === n) {
					$(e).addClass('active');
					$(e).removeClass('button-85');
				} else {
					$(e).removeClass('active');
					$(e).addClass('button-85');
				}
			});
		}


		/* Previous button is easy, just go back */
		form.find('.previous').click(function () {
			document.body.scrollTop = 450 // For Safari
			document.documentElement.scrollTop = 450 // For Chrome, Firefox, IE and Opera
			form.navigateTo(curIndex() - 1)
		})


		/* Next button goes forward iff current block validates */
		form.find('.next').click(function () {
			document.body.scrollTop = 450 // For Safari
			document.documentElement.scrollTop = 450 // For Chrome, Firefox, IE and Opera

			// Check if the current step is the second step and the "Enter Personal Information" tab is active
			if ($('#personInfo.step').hasClass('active')) {
				// AJAX update when clicking "Next" in the second step with the "Enter Personal Information" tab active
				const formData = form.serialize();

				$('.CustomTabs ul.tabs li:eq(0) a').click();

				// console.log(formData)
				$.ajax({
					url: "../update_personal_information.php",
					type: "POST",
					data: formData,
					success: function (response) {
						// Handle success response
						var responseData = JSON.parse(response);

						$('#applicant_name_tab').text(responseData.firstname + " Documents");

						if ($('#applicant_spouse_tab').parent().hasClass('current')) {
							$('.applicant_spouse_tab_item').show(); // Hide the tab content item
						}

						if (responseData.spouse_firstname === '' || responseData.spouse_file_tax === 'No' || responseData.residing_canada === 'No') {
							$('#applicant_spouse_tab').parent().hide(); // Hide the tab anchor
							$('.applicant_spouse_tab_item').hide(); // Hide the tab content item
						} else {
							$('#applicant_spouse_tab').text(responseData.spouse_firstname + " Documents");
							$('#applicant_spouse_tab').parent().show(); // Show the tab anchor
						}

						// Function to get the value of the checked radio button by name
						function getCheckedRadioValue(name) {
							var radio = document.querySelector('input[name="' + name + '"]:checked');
							return radio ? radio.value : null;
						}

						var firstFillingTax = getCheckedRadioValue("first_fillingtax");

						if (firstFillingTax === 'Yes') {
							$('#upload_sin_number_document').show();
						} else {
							$('#upload_sin_number_document').hide();
						}

						var spouseFirstFillingTax = getCheckedRadioValue("spouse_first_tax");

						if (spouseFirstFillingTax === 'Yes') {
							$('#spouse_upload_sin_number_document').show();
						} else {
							$('#spouse_upload_sin_number_document').hide();
						}

					},
					error: function (response) {
						// Handle error response
						console.error(response);
					},
				});
			}

			// $('.CustomTabs ul.tabs li:eq(0) a').click();

			if (
				'validations' in args &&
				typeof args.validations === 'object' &&
				!$.isArray(args.validations)
			) {
				if (
					!('noValidate' in args) ||
					(typeof args.noValidate === 'boolean' && !args.noValidate)
				) {
					form.validate(args.validations)
					if (form.valid() == true) {

						if ($('#personInfo.step').hasClass('active')) {
							$('form input').each(function () {
								var inputName = $(this).attr('name'); // Get the name of the input field
								var inputValue = $(this).val(); // Get the value of the input field

								// Check if the input field has a name and a value
								if (inputName && inputValue) {
									// Check if the input field is a radio button
									if ($(this).attr('type') === 'radio') {
										// Only log the input field if it is checked
										if ($(this).is(':checked')) {
											console.log('Input field "' + inputName + '" has value: ' + inputValue);
											var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
											$.ajax({
												url: '../log_inputs.php',
												type: 'post',
												data: { 'inputName': inputName, 'inputValue': inputValue, 'email': email },
											});
										}
									} else {
										// Log the name and value of the input field
										console.log('Input field "' + inputName + '" has value: ' + inputValue);
										var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
										$.ajax({
											url: '../log_inputs.php',
											type: 'post',
											data: { 'inputName': inputName, 'inputValue': inputValue, 'email': email },
										});
									}
								}
							});
						}

						form.navigateTo(curIndex() + 1)

						// $(this).click();
						$(".form_append_error").empty();
						return true
					}
					createAlert(
						'Please Fill In All Required Fields ',
						'',
						'',
						'danger',
						true,
						true,
						'pageMessages'
					)
					var dangerAlert = `
						<div class="alert alert-danger alert-dismissible bg-danger border-danger text-white alert-label-icon shadow fade show" role="alert">
							<i class="ri-error-warning-line label-icon"></i><strong>Please Fill In All Required Fields</strong>
							<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					`;
					$(".form_append_error").empty().append(dangerAlert);
					var errors = $(".error").get().reverse(); // Get all elements with class 'error' and reverse their order
					var errorNames = new Set(); // Create a new Set to store the names of the input fields

					errors.forEach(function (element) {
						$(element).focus(); // Focus on each element
						if (element.name) { // Check if the name property is not undefined
							errorNames.add(element.name); // Add the name of each element to the Set
						}
					});

					// Convert the Set back to an Array
					var uniqueErrorNames = Array.from(errorNames);

					uniqueErrorNames.forEach(function (name) {
						console.log(name); // Log the name of each unique element
						var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
						$.ajax({
							url: '../log_errors.php',
							type: 'post',
							data: { 'error': name, 'email': email },
							success: function (data) {

							}
						});
					});
					return false
				}
			}
		})
    
    
    
    
    
    
    
       
    


		form.find('.submit').on('click', function (e) {
			e.preventDefault();
			// document.body.scrollTop = 450;
			// document.documentElement.scrollTop = 450;
        
        
        
        
                     
       
    
    

			// Function to get the value of the checked radio button by name
			function getCheckedRadioValue(name) {
				var radio = document.querySelector('input[name="' + name + '"]:checked');
				return radio ? radio.value : null;
			}

			// Your existing form submission logic here
			if (typeof args.beforeSubmit !== 'undefined' && typeof args.beforeSubmit !== 'function') {
				args.beforeSubmit(form, this);
			}

			var uploadIdProofCheck2 = document.getElementById("uploaded-links-input");
			var uploadIdProofContainer = document.getElementById("upload_id_proof");

			if (uploadIdProofCheck2.value.trim() === '') {

				createAlert(
					'Please add at least one ID Proof',
					'',
					'',
					'danger',
					true,
					true,
					'pageMessages'
				);

				uploadIdProofContainer.scrollIntoView();

				var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
				var error = "ID Proof"
				$.ajax({
					url: '../log_file_uploads.php',
					type: 'post',
					data: { 'error': error, 'email': email },
					success: function (data) {

					}
				});

				return false;
			}

			var firstFillingTax = getCheckedRadioValue("first_fillingtax");

			if (firstFillingTax === 'Yes') {

				var uploadSinNumberCheck = document.getElementById("uploaded_sin_number_document");
				var uploadSinNumberContainer = document.getElementById("upload_sin_number_document");

				if (uploadSinNumberCheck.value.trim() === '') {
					createAlert(
						'Please add at least one SIN Number Document',
						'',
						'',
						'danger',
						true,
						true,
						'pageMessages'
					);

					$('.CustomTabs ul.tabs li:eq(0) a').click();
					uploadSinNumberContainer.scrollIntoView();

					var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
					var error = "SIN Number Document"
					$.ajax({
						url: '../log_file_uploads.php',
						type: 'post',
						data: { 'error': error, 'email': email },
						success: function (data) {

						}
					});

					return false;
				}
			}
        
        
        

			
        var rentBenefitValue = getCheckedRadioValue("rent_benefit");
        var rentview= document.getElementById("rent_view");
if (rentBenefitValue == "Yes") {
    let rentAddressFields = document.querySelectorAll(".benefit_rent_address");
    console.log("rent---");
    let allValid = true;

    rentAddressFields.forEach(function (field) {
        let errorMsg = field.nextElementSibling;

        // Check if the field is empty
        if (!field.value.trim()) {
            field.style.border = "2px solid red"; // Add red border
            field.setAttribute("aria-invalid", "true");
            errorMsg.style.display = "block";
            allValid = false;
            
        } else {
            field.style.border = ""; // Remove red border
            field.setAttribute("aria-invalid", "false");
            errorMsg.style.display = "none";
        }

        // Hide error when user starts typing
        field.addEventListener("input", function () {
            field.style.border = ""; // Remove red border
            field.setAttribute("aria-invalid", "false");
            errorMsg.style.display = "none";
        });
    });

    if (!allValid) {
        createAlert(
            "Please Fill In All Required Fields", // Single alert for all fields
            "",
            "",
            "danger",
            true,
            true,
            "pageMessages"
        );
    
        $('.CustomTabs ul.tabs li:eq(0) a').click();
        rentview.scrollIntoView();
        
        return false;
    }
}

        
        

        
        
        
        
        
        
        
        
        
 var rentBenefitValue = getCheckedRadioValue("spouse_rent_benefit");
        var spouserentview= document.getElementById("spouse_rent_view");
if (rentBenefitValue == "Yes") {
    let rentAddressFields = document.querySelectorAll(".spouse_benefit_rent_address");
    console.log("rent---");
    let allValid = true;

    rentAddressFields.forEach(function (field) {
        let errorMsg = field.nextElementSibling;

        // Check if the field is empty
        if (!field.value.trim()) {
            field.style.border = "2px solid red"; // Add red border
            field.setAttribute("aria-invalid", "true");
            errorMsg.style.display = "block";
            allValid = false;
           
        } else {
            field.style.border = ""; // Remove red border
            field.setAttribute("aria-invalid", "false");
            errorMsg.style.display = "none";
        }

        // Hide error when user starts typing
        field.addEventListener("input", function () {
            field.style.border = ""; // Remove red border
            field.setAttribute("aria-invalid", "false");
            errorMsg.style.display = "none";
        });
    
  
    });

    if (!allValid) {
        createAlert(
            "Please Fill In All Required Fields", // Single alert for all fields
            "",
            "",
            "danger",
            true,
            true,
            "pageMessages"
        );
    
       
         spouserentview.scrollIntoView();
        return false;
    }
}

        
        
        
        
			// Get the value of the radio button named "income_delivery"
			var incomeDeliveryValue = getCheckedRadioValue("income_delivery");

			if (incomeDeliveryValue === "Yes") {

				var annualTaxSummyCheck2 = document.getElementById("uploaded-delivery-annual-tax");
				var annualTaxSummyContainer = document.getElementById("upload_delivery_annual_tax");

				if (annualTaxSummyCheck2.value.trim() === '') {

					createAlert(
						'Please add at least one Annual Tax Summary',
						'',
						'',
						'danger',
						true,
						true,
						'pageMessages'
					);

					annualTaxSummyContainer.scrollIntoView();

					var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
					var error = "Annual Tax Summary"
					$.ajax({
						url: '../log_file_uploads.php',
						type: 'post',
						data: { 'error': error, 'email': email },
						success: function (data) {

						}
					});

					return false;
				}

			}

			if (incomeDeliveryValue === null) {

				// createAlert(
				// 	'Please Fill In All Required Fields',
				// 	'',
				// 	'',
				// 	'danger',
				// 	true,
				// 	true,
				// 	'pageMessages'
				// );
				if (!form.valid()) {

					createAlert(
						'Please Fill In All Required Fields',
						'',
						'',
						'danger',
						true,
						true,
						'pageMessages'
					);

					var errors = $(".error").get().reverse(); // Get all elements with class 'error' and reverse their order
					var errorNames = new Set(); // Create a new Set to store the names of the input fields

					errors.forEach(function (element) {
						$(element).focus(); // Focus on each element 

						if (element.name) { // Check if the name property is not undefined
							errorNames.add(element.name); // Add the name of each element to the Set
						}
					});

					// Convert the Set back to an Array
					var uniqueErrorNames = Array.from(errorNames);

					uniqueErrorNames.forEach(function (name) {
						console.log(name); // Log the name of each unique element
						var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
						$.ajax({
							url: '../log_errors.php',
							type: 'post',
							data: { 'error': name, 'email': email },
							success: function (data) {

							}
						});
					});
					$('.CustomTabs ul.tabs li:eq(0) a').click();
					return false;
				}
			} else {

				var spouseCheckFileTax = getCheckedRadioValue("spouse_file_tax");
				var CheckresidingCanada = getCheckedRadioValue("residing_canada");
				var maritalStatusUser = getCheckedRadioValue("marital_status");

				// console.log("Marital Status ", maritalStatusUser);
				// console.log(spouseCheckFirstName, spouseCheckFileTax, CheckresidingCanada)

				if (spouseCheckFileTax === 'No' || CheckresidingCanada === 'No' || spouseCheckFileTax === null || maritalStatusUser === "Single" || maritalStatusUser === "Separated" || maritalStatusUser === "Widow" || maritalStatusUser === "Divorced") {
					// Show the confirmation modal
					$('#confirmationModal').modal('show');

					const formData = form.serialize();
					// console.log(formData)
					$.ajax({
						url: "../update_documents_info.php",
						type: "POST",
						data: formData,
						success: function (response) {
							// Handle success response
							console.log(response);
						},
						error: function (response) {
							// Handle error response
							console.error(response);
						},
					});

				} else {

					var spouseTabParent = document.querySelector("#applicant_spouse_tab").parentNode;
					var spouseTabParentDisplayStyle = window.getComputedStyle(spouseTabParent).display;

					if (spouseTabParentDisplayStyle !== "none") {
						var uploadIdProofCheck3 = document.getElementById("spouse_uploaded-links-input");
						var spouseUploadIdProofContainer = document.getElementById("spouse_upload_id_proof");

						if (uploadIdProofCheck3.value.trim() === '') {
							createAlert(
								'Please add at least one Spouse ID Proof',
								'',
								'',
								'danger',
								true,
								true,
								'pageMessages'
							);

							spouseUploadIdProofContainer.scrollIntoView();

							var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
							var error = "Spouse ID Proof"
							$.ajax({
								url: '../log_file_uploads.php',
								type: 'post',
								data: { 'error': error, 'email': email },
								success: function (data) {

								}
							});

							return false;
						} else {
							var spouseFirstFillingTax = getCheckedRadioValue("spouse_first_tax");

							if (spouseFirstFillingTax === 'Yes') {
								var uploadSpouseSinNumberCheck = document.getElementById("spouse_sin_number_document");
								var uploadSpouseSinNumberContainer = document.getElementById("spouse_upload_sin_number_document");

								if (uploadSpouseSinNumberCheck.value.trim() === '') {
									createAlert(
										'Please add at least one SIN Number Document',
										'',
										'',
										'danger',
										true,
										true,
										'pageMessages'
									);
									$('.CustomTabs ul.tabs li:eq(1) a').click();
									uploadSpouseSinNumberContainer.scrollIntoView();

									var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
									var error = "Spouse SIN Number Document"
									$.ajax({
										url: '../log_file_uploads.php',
										type: 'post',
										data: { 'error': error, 'email': email },
										success: function (data) {

										}
									});
									return false;
								}
							}

							// Get the value of the radio button named "spouse_income_delivery"
							var incomeSpouseDeliveryValue = getCheckedRadioValue("spouse_income_delivery");
							// console.log("Spouse Income delivery value: ", incomeSpouseDeliveryValue);

							if (incomeSpouseDeliveryValue === "Yes") {

								var annualSpouseTaxSummyCheck2 = document.getElementById("spouse_uploaded-delivery-annual-tax");
								var annualSpouseTaxSummyContainer = document.getElementById("spouse_upload_delivery_annual_tax");

								if (annualSpouseTaxSummyCheck2.value.trim() === '') {

									createAlert(
										'Please add at least one Spouse Annual Tax Summary',
										'',
										'',
										'danger',
										true,
										true,
										'pageMessages'
									);

									annualSpouseTaxSummyContainer.scrollIntoView();

									var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
									var error = "Spouse Annual Tax Summary"
									$.ajax({
										url: '../log_file_uploads.php',
										type: 'post',
										data: { 'error': error, 'email': email },
										success: function (data) {

										}
									});

									return false;
								}

							}

							if (incomeSpouseDeliveryValue === null) {
								if (!form.valid()) {

									createAlert(
										'Please Fill In All Required Fields',
										'',
										'',
										'danger',
										true,
										true,
										'pageMessages'
									);

									var errors = $(".error").get().reverse(); // Get all elements with class 'error' and reverse their order
									var errorNames = new Set(); // Create a new Set to store the names of the input fields

									errors.forEach(function (element) {
										$(element).focus(); // Focus on each element 

										if (element.name) { // Check if the name property is not undefined
											errorNames.add(element.name); // Add the name of each element to the Set
										}
									});

									// Convert the Set back to an Array
									var uniqueErrorNames = Array.from(errorNames);

									uniqueErrorNames.forEach(function (name) {
										console.log(name); // Log the name of each unique element
										var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
										$.ajax({
											url: '../log_errors.php',
											type: 'post',
											data: { 'error': name, 'email': email },
											success: function (data) {

											}
										});
									});
									$('.CustomTabs ul.tabs li:eq(1) a').click();
									return false;
								}
							} else {
								// Show the confirmation modal
								$('#confirmationModal').modal('show');

								const formData = form.serialize();
								// console.log(formData)
								$.ajax({
									url: "../update_documents_info.php",
									type: "POST",
									data: formData,
									success: function (response) {
										// Handle success response
										console.log(response);
									},
									error: function (response) {
										// Handle error response
										console.error(response);
									},
								});
							}
						}


					}



				}
			}


		});


		// Add a separate click event for #btnConfirmationModal
		$('#btnConfirmationModal').on('click', function () {
			// Close the confirmation modal
			$('#confirmationModal').modal('hide');

			$('form input').each(function () {
				var inputName = $(this).attr('name'); // Get the name of the input field
				var inputValue = $(this).val(); // Get the value of the input field

				// Check if the input field has a name and a value
				if (inputName && inputValue) {
					// Check if the input field is a radio button
					if ($(this).attr('type') === 'radio') {
						// Only log the input field if it is checked
						if ($(this).is(':checked')) {
							console.log('Input field "' + inputName + '" has value: ' + inputValue);
							var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
							$.ajax({
								url: '../log_inputs.php',
								type: 'post',
								data: { 'inputName': inputName, 'inputValue': inputValue, 'email': email },
							});
						}
					} else {
						// Log the name and value of the input field
						console.log('Input field "' + inputName + '" has value: ' + inputValue);
						var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
						$.ajax({
							url: '../log_inputs.php',
							type: 'post',
							data: { 'inputName': inputName, 'inputValue': inputValue, 'email': email },
						});
					}
				}
			});

			const formData = form.serialize();
			const url = form.attr('action');
			const method = form.attr('method');

			$.ajax({
				url: url,
				type: method,
				data: formData,
				beforeSend: function () {
					const loader = document.querySelector('.loader');
					loader.classList.remove('loader--hidden');
				},
				success: function (response) {
					if (response) {
						$('#emailSentModal').modal('show');
					}
					$('html, body').animate({
						scrollTop: 0,
					}, 100);
					form.navigateTo(0);

				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error(textStatus, errorThrown);
				},
				complete: function () {
					const loader = document.querySelector('.loader');
					loader.classList.add('loader--hidden');
				},
			});

			return false;
		});


		/*By default navigate to the tab 0, if it is being set using defaultStep property*/
		typeof args.defaultStep === 'number'
			? form.navigateTo(args.defaultStep)
			: null

		form.noValidate = function () { }
		return form
	}

	//Get the button
	let mybutton = document.getElementById("btn-back-to-top");
	let myContinueButton = document.getElementById("btn_fixed_continue");

	// When the user scrolls down 20px from the top of the document, show the button
	window.onscroll = function () {
		scrollFunction();
		scrollButtonFunction();
	};

	function scrollFunction() {
		if (!mybutton) return; // Safe fix: null check
		if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
			mybutton.style.display = "block";
		} else {
			mybutton.style.display = "none";
		}
	}

	function scrollButtonFunction() {
		if (document.body.scrollTop > 350 || document.documentElement.scrollTop > 350) {
			// Check if the current step is the first step of the multi-step form
			if (curIndex() === 0) {

				// Get the multi_step_button element
				var multiStepButton = document.getElementById("multi_step_button");
			if (!multiStepButton) return;

				// Get the position of the multi_step_button element relative to the viewport
				var buttonPosition = multiStepButton.getBoundingClientRect();

				// Check if any part of the multi_step_button element is visible in the viewport
				var isVisible = (
					buttonPosition.top >= 0 &&
					buttonPosition.left >= 0 &&
					buttonPosition.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
					buttonPosition.right <= (window.innerWidth || document.documentElement.clientWidth)
				);

				// If the multi_step_button element is visible, do something
				if (isVisible) {
					// console.log("Multi-step buttons are visible in the viewport");
					// You can add additional actions here, such as showing the buttons
					if (myContinueButton) myContinueButton.style.display = "none";
				} else {
					// console.log("Multi-step buttons are not visible in the viewport");
					// You can add additional actions here, such as hiding the buttons
					if (buttonPosition.top <= 0) {
						console.log("Past");
						if (myContinueButton) myContinueButton.style.display = "none";
					} else {
						if (myContinueButton) myContinueButton.style.display = "flex";
					}
				}


			} else {
				if (myContinueButton) myContinueButton.style.display = "none";
			}
		} else {
			if (myContinueButton) myContinueButton.style.display = "none";
		}
	}

	// Function to get the current step index
	function curIndex() {
		return $('.tab').index($('.tab.current'));
	}

	// When the user clicks on the button, scroll to the top of the document
	if (mybutton) { // Safe fix: null check
	mybutton.addEventListener("click", backToTop);
	}

	// Function to scroll to the top of the document
	function backToTop() {
		document.body.scrollTop = 0;
		document.documentElement.scrollTop = 0;
	}

	// When the user clicks on the continue button, trigger the click event on the next button of your multi-step form
	if (myContinueButton) {
		myContinueButton.addEventListener("click", continuePersonalInformation);
	}

	// Function to continue to the next step of the multi-step form
	function continuePersonalInformation() {
		$('.next').click();
	}

})(jQuery)
