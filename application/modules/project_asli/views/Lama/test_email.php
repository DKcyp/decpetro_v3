<html>

<head>
    <title>Kirim Email dengan CodeIgniter</title>
</head>

<body>
    <div style="padding: 5px 30px;">
        <h1>Kirim Email dengan Framework Codeigniter</h1>
        <hr />
        <form action="<?= base_url('project/test_email/send') ?>" enctype="multipart/form-data" method="POST"   >
            <div style="margin-bottom: 10px;">
                <label>Kepada</label><br />
                <input type="email" name="email_penerima" placeholder="Email Penerima" style="margin-top: 5px;width: 400px" />
            </div>
            <div style="margin-bottom: 10px;">
                <label>Subjek</label><br />
                <input type="text" name="subjek" placeholder="Subjek" style="margin-top: 5px;width: 400px" />
            </div>
            <div style="margin-bottom: 10px;">
                <label>Pesan</label><br />
                <textarea name="pesan" placeholder="Pesan" rows="8" style="margin-top: 5px;width: 400px"></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label>Attachment</label><br />
                <input type="file" name="attachment" style="margin-top: 5px;width: 400px" />
            </div>
            <hr />
            <button type="submit">KIRIM EMAIL</button>
        </form>
    </div>
</body>

</html>