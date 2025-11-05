// This sample uses the Places Autocomplete widget to:
// 1. Help the user select a place
// 2. Retrieve the address components associated with that place
// 3. Populate the form fields with those address components.
// This sample requires the Places library, Maps JavaScript API.
// Include the libraries=places parameter when you first load the API.
// For example: <script
// src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
let autocomplete
let address1Field
let address2Field
let postalField

function initAutocomplete() {
  address1Field = document.querySelector('#ship_address')
  address2Field = document.querySelector('#address2')
  postalField = document.querySelector('#postcode')
  // Create the autocomplete object, restricting the search predictions to
  // addresses in the US and Canada.
  autocomplete = new google.maps.places.Autocomplete(address1Field, {
    componentRestrictions: { country: ['us', 'ca'] },
    fields: ['address_components', 'geometry'],
    types: ['address'],
  })
  // address1Field.focus()
  // When the user selects an address from the drop-down, populate the
  // address fields in the form.
  autocomplete.addListener('place_changed', fillInAddress)

  // const input = document.querySelector('.pac-input')
  const input1 = document.querySelector('.move_from')
  const input2 = document.querySelector('.move_to')
  // const input3 = document.querySelector('.rent_address_search')

  // const searchBox = new google.maps.places.SearchBox(input)
  const searchBox1 = new google.maps.places.Autocomplete(input1, {
    componentRestrictions: { country: ['us', 'ca'] },
    fields: ['address_components', 'geometry'],
    types: ['address'],
  })
  const searchBox2 = new google.maps.places.Autocomplete(input2, {
    componentRestrictions: { country: ['us', 'ca'] },
    fields: ['address_components', 'geometry'],
    types: ['address'],
  })

  var variableAuto = document.getElementsByClassName('rent_address_search')

  $('body').on('focus', '.rent_address_search', function () {
    // console.log($(this))
    for (var j = 0; j < variableAuto.length; j++) {
      new google.maps.places.Autocomplete(variableAuto[j], {
        componentRestrictions: { country: ['us', 'ca'] },
        fields: ['address_components', 'geometry'],
        types: ['address'],
      })
    }
  })

  var spouse_variableAuto = document.getElementsByClassName('spouse_rent_address_search')

  $('body').on('focus', '.spouse_rent_address_search', function () {
    // console.log($(this))
    for (var j = 0; j < spouse_variableAuto.length; j++) {
      new google.maps.places.Autocomplete(spouse_variableAuto[j], {
        componentRestrictions: { country: ['us', 'ca'] },
        fields: ['address_components', 'geometry'],
        types: ['address'],
      })
    }
  })
}

function fillInAddress() {
  // Get the place details from the autocomplete object.
  const place = autocomplete.getPlace()
  let address1 = ''
  let postcode = ''

  // Get each component of the address from the place details,
  // and then fill-in the corresponding field on the form.
  // place.address_components are google.maps.GeocoderAddressComponent objects
  // which are documented at http://goo.gle/3l5i5Mr
  for (const component of place.address_components) {
    // @ts-ignore remove once typings fixed
    const componentType = component.types[0]

    switch (componentType) {
      case 'street_number': {
        address1 = `${component.long_name} ${address1}`
        break
      }

      case 'route': {
        address1 += component.short_name
        break
      }

      case 'postal_code': {
        postcode = `${component.long_name}${postcode}`
        break
      }

      case 'postal_code_suffix': {
        postcode = `${postcode}-${component.long_name}`
        break
      }
      case 'locality':
        document.querySelector('#locality').value = component.long_name
        break
      case 'administrative_area_level_1': {
        document.querySelector('#state').value = component.short_name
        break
      }
      case 'country':
        document.querySelector('#country').value = component.long_name
        break
    }
  }

  address1Field.value = address1
  postalField.value = postcode
  // After filling the form with address components from the Autocomplete
  // prediction, set cursor focus on the second address line to encourage
  // entry of subpremise information such as apartment, unit, or floor number.
  address2Field.focus()
}

window.initAutocomplete = initAutocomplete
