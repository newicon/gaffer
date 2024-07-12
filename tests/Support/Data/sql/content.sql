INSERT INTO page(stub, title, description, content, active, redirect)
VALUES
    ('/', 'home', 'homepage', '<h1>Gaffer Demo</h1><p>This is simply a demo implementation!<br><a href="/hello">Hello!</a></p>', 1, null),
    ('/hello', 'hello!', 'hellopage', '<h1>Gaffer Hello</h1><p>Hello! This page is just to illustrate routing works!<br><a href="/">Back to home</a></p>', 1, null)
