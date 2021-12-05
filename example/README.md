## Docker demo

This is a working config example, not suitable for production.
It installs simplesamlphp with the oauth2 module and defines a sample application to go through the oauth2 flow.

### Instructions

```
IMAGE_TAG="demo"
docker build . -t "$IMAGE_TAG"
docker run -p8000:8000 -t "$IMAGE_TAG"

# Open you browser at http://127.0.0.1:8000/dummyclient
```
