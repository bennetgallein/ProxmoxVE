# [5.1.0](https://github.com/bennetgallein/ProxmoxVE/compare/v5.0.0...5.1.0) (2023-12-05)


### Features

* add parsing of port ([5b100a5](https://github.com/bennetgallein/ProxmoxVE/commit/5b100a5d7898978df61a05285ca259a0e7c6032c))

v3.0.0
------

- PHP 5.4 required.
- Proxmox API client logic rewritten (but the usage is pretty much the same).
- GuzzleHttp used for sending HTTP requests.
- Credentials default form is now an array not an object (read the docs).
- Better docs (compliant with this new release).


v2.1.1
------

- Small bug fixed, using an Eloquent model as a custom credentials object was not valid.
- Code coverage and code quality service integrated.


v2.1.0
------

- Now it is possible to specify the desired return type when creating Proxmox object, setters and getters for return type were added.
- Added docs about getters & setters and other tricks of the Proxmox client object.
- Now you can create a Proxmox API client object with a custom credentials object.


v2.0.0
------

- Namespace has changed, vendor name is not used anymore.
- The class that handles the API calls is now *Proxmox* not *ProxmoxVE*.
- Functions were renamed, instead of `get`, `post`, `put` and `delete` you use `get`, `create`, `set`, `delete` (to keep consistency with the *pvesh CLI Tool*).
- Documentation improved.
- Library now returns the errors messages instead of throwing an exception when a request was not successful.
- Source code refactored and respective tests were added.


v1.1.1
------

- Fixed bug when CURL was not enabled library used to crash.


v1.1.0
------

- Add check to see if CURL is enabled.
- Added capability to change Credentials after ProxmoxVE object creation.


v1.0.0
------

- Release of ProxmoxVE API Client.
- Use get, post, put, delete functions to request resources.
- Namespace includes vendor ZzAntares.
