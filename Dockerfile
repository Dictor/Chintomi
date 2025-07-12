FROM golang:1.24-alpine
COPY . /chintomi

WORKDIR / 
RUN mkdir -p chintomi/content

WORKDIR /chintomi
RUN ["go", "build"]
ENTRYPOINT ["/bin/sh", "-c"]
CMD ["./chintomi"]