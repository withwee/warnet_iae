# Build stage
FROM golang:1.21-alpine AS builder

# Install dependencies including protoc
RUN apk add --no-cache git gcc musl-dev protobuf protobuf-dev

WORKDIR /app

# Copy go mod files first
COPY go.mod ./

# Download dependencies
RUN go mod download

# Install protoc-gen-go and protoc-gen-go-grpc after go mod
RUN go install google.golang.org/protobuf/cmd/protoc-gen-go@v1.31.0 && \
    go install google.golang.org/grpc/cmd/protoc-gen-go-grpc@v1.3.0

# Copy everything
COPY . .

# Generate proto files
RUN protoc --go_out=. --go_opt=paths=source_relative \
    --go-grpc_out=. --go-grpc_opt=paths=source_relative \
    proto/chat.proto

# Build the application
RUN CGO_ENABLED=0 GOOS=linux go build -a -installsuffix cgo -o chat-server .

# Runtime stage
FROM alpine:latest

RUN apk --no-cache add ca-certificates

WORKDIR /root/

# Copy binary from builder
COPY --from=builder /app/chat-server .

# Copy .env file if exists, otherwise use example
COPY --from=builder /app/.env* ./

EXPOSE 50051

CMD ["./chat-server"]
