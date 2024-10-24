// What fetch returns - 1st level
export type FetchResponse = {
	data: AdminAjaxResponse;
}

// What the BE returns - 2nd level
export type AdminAjaxResponse = SuccessResponse | ErrorResponse;

// If it's a success - 2nd level
type SuccessResponse = {
	success: true;
	data: Result[];
}

// If it's an error - 2nd level
type ErrorResponse = {
	success: false;
	data: Error;
}

// The results (comes in an array) - 3rd level
export type Result = {
	result: {
		title: string;
		url: string;
		primary_artist: {
			name: string;
			url: string;
		};
		header_image_thumbnail_url: string;
	};
};

// The error message - 3rd level
export type Error = {
	message: string;
	description: string;
	status_code: number;
};

// TODO: Do I want to model a fetch failure?
