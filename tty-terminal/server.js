const tty = require('tty.js');

const server = tty.createServer({
  term: 'xterm', // Terminal type
  host: '0.0.0.0',
  port: 3000, // Choose a port
  shell: '/bin/bash' // or any shell you prefer
});

server.listen(() => {
  console.log('TTY.js server started at port 3000');
});

