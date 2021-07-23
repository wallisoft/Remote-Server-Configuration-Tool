# Remote-Server-Configuration-Tool
PHP script with web frontend to simplify remote server configuration

This is a web frontend to remote_conf.php which accepts 4 arguments :-  Server IP/Hostname, Username, Password, RemoteCommand (optional). 

To set the web Admin password edit $web_password at the start of remote_conf.php.

The 'Run Text' button will remote execute any shell commands entered in the TextBox.

The 'Run Script' button will read the most recently uploaded script.txt and sequentially execute each line on the remote server with the configured ENV variables set.

All output from remote_conf.php is saved in rsct.log which can be viewed or cleared with the 'View Log' and 'Clear Log' buttons.


script.txt accepts the following keywords to perform actions on the remote server :-

ENV - Set environment variables
RUN - execute the shell commands on remotes server.
COPY - rsync files from webserver to remote server.
EXPOSE - Open tcp port in iptables on remote server.


The script format is the same as Docker so you can use a Docker script to configure any type of linux server.

