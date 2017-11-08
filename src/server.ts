import xpr = require("express");
import multer = require("multer");
import { json, raw, urlencoded } from "body-parser";
import path = require("path");
import { createWriteStream, createReadStream, unlink } from "fs";
import { exec } from "child_process";
import { platform, tmpdir } from "os";
import pdf = require("pdf-merge");
import { Readable, Stream } from "stream";
import { promisify } from "util";

const app = xpr();
const upl = multer({ dest: path.join(__dirname, "uploads") });

class PdfConverter {
	cmdLine = {
		"all": "env HOME=/tmp /opt/libreoffice5.4/program/soffice",
		"win32": "\"C:/Program Files/LibreOffice 5/program/soffice\""
	};

	constructor() {
		app.use(json());
		app.use(urlencoded());
		app.use(upl.any());
		app.use(raw());
	}

	convertFile(thePdfFile: string): Promise<string> {
		return new Promise((resolve, reject) => {
			let execAsync = promisify(exec),
				newLocal = this.cmdLine[platform() == "win32" ? "win32" : "all"],
				f = path.parse(thePdfFile),
				outFile = path.join(f.dir, f.name + ".pdf");
			execAsync(`${newLocal} --headless --convert-to pdf --outdir ${f.dir} ${thePdfFile}`).then(out => {
				console.log(out.stdout);
				console.error(out.stderr);
				unlink(thePdfFile, () => { });
				resolve(outFile);
			});
		});
	}

	async mergeFiles(files: string[]): Promise<Stream> {
		var convertedFile = await Promise.all(files.map(file => this.convertFile(file)));
		return <Promise<Stream>>pdf(convertedFile, { output: "Stream" });
	}

	dispatch() {
		app.post("/pdf", (req, res) => {
			if (!req.is("multipart/form-data")) {
				var thePdfFile = path.join(__dirname, `word_file_${String(Math.round(Math.random() * 100000))}.docx`);
				req.pipe(createWriteStream(thePdfFile).on("close", () => {
					this.convertFile(thePdfFile).then(result => res.sendFile(result));
				}));
			} else if (Array.isArray(req.files)) {
				this.mergeFiles(req.files.map(file => file.path)).then((stream: Stream) => {
					res.type("application/pdf");
					stream.pipe(res);
				});
			}
		});

		app.listen(3000, () => {
			console.log("Listening on port 3000");
		});
	}
}

(new PdfConverter).dispatch();
