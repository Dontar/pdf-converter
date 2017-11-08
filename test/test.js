"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const http_1 = require("http");
const fs_1 = require("fs");
const FormData = require("form-data");
var opts = {
    hostname: 'localhost',
    port: 3000,
    path: '/pdf',
    method: 'POST',
    headers: {
        'Content-Type': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    }
};
var req = http_1.request(opts, res => {
    res.pipe(fs_1.createWriteStream(`${__dirname}/doc.pdf`));
});
(fs_1.createReadStream(`${__dirname}/h_agreement.docx`)).pipe(req);
var formData = new FormData();
formData.append("file1", fs_1.createReadStream(`${__dirname}/contract.docx`));
formData.append("file2", fs_1.createReadStream(`${__dirname}/h_agreement.docx`));
formData.submit("http://localhost:3000/pdf", (e, r) => {
    r.pipe(fs_1.createWriteStream(`${__dirname}/multi.pdf`));
});
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidGVzdC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzIjpbInRlc3QudHMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6Ijs7QUFBQSwrQkFBK0M7QUFDL0MsMkJBQXlEO0FBQ3pELHNDQUF1QztBQUV2QyxJQUFJLElBQUksR0FBbUI7SUFDMUIsUUFBUSxFQUFFLFdBQVc7SUFDckIsSUFBSSxFQUFFLElBQUk7SUFDVixJQUFJLEVBQUUsTUFBTTtJQUNaLE1BQU0sRUFBRSxNQUFNO0lBQ2QsT0FBTyxFQUFFO1FBQ1IsY0FBYyxFQUFFLHlFQUF5RTtLQUN6RjtDQUNELENBQUM7QUFDRixJQUFJLEdBQUcsR0FBRyxjQUFPLENBQUMsSUFBSSxFQUFFLEdBQUcsQ0FBQyxFQUFFO0lBQzdCLEdBQUcsQ0FBQyxJQUFJLENBQUMsc0JBQWlCLENBQUMsR0FBRyxTQUFTLFVBQVUsQ0FBQyxDQUFDLENBQUM7QUFDckQsQ0FBQyxDQUFDLENBQUM7QUFFSCxDQUFDLHFCQUFnQixDQUFDLEdBQUcsU0FBUyxtQkFBbUIsQ0FBQyxDQUFDLENBQUMsSUFBSSxDQUFDLEdBQUcsQ0FBQyxDQUFDO0FBRTlELElBQUksUUFBUSxHQUFHLElBQUksUUFBUSxFQUFFLENBQUM7QUFDOUIsUUFBUSxDQUFDLE1BQU0sQ0FBQyxPQUFPLEVBQUUscUJBQWdCLENBQUMsR0FBRyxTQUFTLGdCQUFnQixDQUFDLENBQUMsQ0FBQztBQUN6RSxRQUFRLENBQUMsTUFBTSxDQUFDLE9BQU8sRUFBRSxxQkFBZ0IsQ0FBQyxHQUFHLFNBQVMsbUJBQW1CLENBQUMsQ0FBQyxDQUFDO0FBQzVFLFFBQVEsQ0FBQyxNQUFNLENBQUMsMkJBQTJCLEVBQUUsQ0FBQyxDQUFDLEVBQUUsQ0FBQyxFQUFFLEVBQUU7SUFDckQsQ0FBQyxDQUFDLElBQUksQ0FBQyxzQkFBaUIsQ0FBQyxHQUFHLFNBQVMsWUFBWSxDQUFDLENBQUMsQ0FBQztBQUNyRCxDQUFDLENBQUMsQ0FBQyJ9