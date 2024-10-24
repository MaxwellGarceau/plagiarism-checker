export type AdminAjaxResponse = {
	data: {
		success: boolean; // Both success and error
		data: Result[]|Error;
	};
}

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

export type Error = {
	message: string;
	description: string;
	status_code: number;
}
