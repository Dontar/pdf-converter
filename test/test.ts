import { request, RequestOptions } from "http";
import { createWriteStream, createReadStream } from "fs";
import FormData = require("form-data");

var opts: RequestOptions = {
	hostname: 'localhost',
	port: 3000,
	path: '/pdf',
	method: 'POST',
	headers: {
		'Content-Type': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
	}
};
var req = request(opts, res => {
	res.pipe(createWriteStream(`${__dirname}/doc.pdf`));
});

(createReadStream(`${__dirname}/h_agreement.docx`)).pipe(req);

var formData = new FormData();
formData.append("file1", createReadStream(`${__dirname}/contract.docx`));
formData.append("file2", createReadStream(`${__dirname}/h_agreement.docx`));
formData.submit("http://localhost:3000/pdf", (e ,r) => {
	r.pipe(createWriteStream(`${__dirname}/multi.pdf`));
});
