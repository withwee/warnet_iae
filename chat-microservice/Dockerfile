# Build stage
FROM golang:1.21-alpine AS builder

RUN apk add --no-cache git gcc musl-dev protobuf protobuf-dev

WORKDIR /app

#  Copy only go.mod first
COPY go.mod ./

# Copy source that doesn't depend on proto
COPY auth ./auth/
COPY db ./db/
COPY redis ./redis/
COPY proto/chat.proto ./proto/

# Install protoc plugins FIRST
RUN go install google.golang.org/protobuf/cmd/protoc-gen-go@v1.31.0 && \
    go install google.golang.org/grpc/cmd/protoc-gen-go-grpc@v1.3.0

ENV PATH="${PATH}:/root/go/bin"

# Generate proto files
RUN protoc --go_out=. --go_opt=paths=source_relative \
    --go-grpc_out=. --go-grpc_opt=paths=source_relative \
    proto/chat.proto

# Now copy rest of code
COPY server ./server/
COPY main.go ./

# Let Go generate go.sum and download deps
RUN go mod tidy && go mod download

# Build
RUN CGO_ENABLED=0 GOOS=linux go build -o chat-server .

# Runtime
FROM alpine:latest

RUN apk --no-cache add ca-certificates

WORKDIR /root/

COPY --from=builder /app/chat-server .
COPY --from=builder /app/.env* ./ 

EXPOSE 50051

CMD ["./chat-server"]
