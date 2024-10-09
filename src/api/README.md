## Class JW_Player_API

This class is used for constructing an object for making an API call to the JW Player API. Simply pass your API key and API Secret on instantiation. Please reference the interface `API_Requester` for the required methods. This object may be used to pass to the `JW_Player` adapter.

## Class Request

This class will handle the generation of the URL for performing an API request. The API response will call the `parse_response` method which will subsequently call the `parse_success()` or `parse_error()` method of the `API_Requester` object that is used to construct this object.
