declare module "pdf-merge"
{
	import { Stream } from "stream";
	function PDFMerge(files: string[], options?: { output: "Stream" | string }): PromiseLike<Buffer | Stream>;
	export = PDFMerge;
}
